<?php

namespace tpl;

class Scope {
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

    function evaluate($expression) {
        return $this->evaluate_sub($expression, $this->getData());
    }

    private function evaluate_sub($expression_8slSL29x, $data_8slSL29x) {
        foreach ($data_8slSL29x as $key_8slSL29x => $value_8slSL29x) {
            ${ $key_8slSL29x } = $value_8slSL29x;
        }

        return @eval('return ' . $expression_8slSL29x . ';');
    }

    public function addLayer($data = []) {
        $this->layers[] = $data;
    }

    public function removeLayer() {
        if (count($this->layers) == 1) {
            throw new \Exception("can't remove last layer");
        }

        array_pop($this->layers);
    }

    public function getEntry($key) {
        foreach (array_reverse($this->layers) as $layer) {
            if (isset($layer[$key])) {
                return $layer[$key];
            }
        }

        return null;
    }

    public function getData() {
        $data = [];
        foreach ($this->layers as $layer) {
            foreach ($layer as $key => $value) {
                $data[$key] = $value;
            }
        }

        return $data;
    }

    public function __toString() {
        return '' . print_r($this->layers, true);
    }
}
