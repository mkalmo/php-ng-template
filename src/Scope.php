<?php

class Scope {
    private $data;
    private $layers = [];

    public function __construct($data = []) {
        $this->addLayer($data);
    }

    public function replaceCurlyExpression($text) {
        return preg_replace_callback(
            '|{{\s*(\$[^\s]+)\s*}}|im',
            function ($matches) {
                return $this->evaluate($matches[1]);
            },
            $text);
    }

    public function evaluate($expression) {
        $parts = preg_split('/(?=\[)|->/' , $expression);
        $rootString = array_shift($parts);

        $negated = false;
        if (preg_match('/^!\s*/' , $rootString)) {
            $rootString = preg_replace('/^!\s*/', '', $rootString);
            $negated = true;
        }

        $result = $this->getEntry($rootString);

        if ($result === null) {
            return $negated ? 1 : null;
        }

        foreach ($parts as $part) {
            if (preg_match('/^\w+$/' , $part)) {
                $result = $result->$part;
            }
            if (preg_match('/^(\w+)\(\)$/', $part, $matches)) {
                $methodName = $matches[1];
                $result = $result->$methodName();
            }
            if (preg_match('/^\[([^\]]+)\]$/' , $part, $matches)) {
                $index = preg_replace('/["\']/', '', $matches[1]);
                $result = isset($result[$index]) ? $result[$index] : '';
            }
        }

        return $negated ? !$result : $result;
    }

    public function addLayer($data = []) {
        $this->layers []= $data;
        $this->data = & $this->layers[sizeof($this->layers) - 1];
    }

    public function removeLayer() {
        if (count($this->layers) == 1) {
            throw new \Exception("can't remove last layer");
        }

        array_pop($this->layers);

        $this->data = & $this->layers[sizeof($this->layers) - 1];
    }

    public function addEntry($key, $value) {
        $this->data[$key] = $value;
    }

    public function getEntry($key) {
        foreach (array_reverse($this->layers) as $layer) {
            if (isset($layer[$key])) {
                return $layer[$key];
            }
        }

        return null;
    }

    public function removeEntry($key) {
        unset($this->data[$key]);
    }

    public function __toString() {
        return '' . print_r($this->layers, true);
    }
}
