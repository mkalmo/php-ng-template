<?php

namespace tplLib;

class Scope {
    public $mainTemplatePath;

    private $layers = [];
    private $translations;

    public function __construct($data = [],
                                $translations = [],
                                $mainTemplatePath = null) {

        $this->translations = $translations;

        $this->addLayer($data);

        $this->mainTemplatePath = $mainTemplatePath;
    }

    public function replaceCurlyExpression($text) {
        return preg_replace_callback(
            '|{{(.+?)}}|im',
            function ($matches) {
                $result = $this->evaluate(trim($matches[1]));
                return htmlspecialchars($result, ENT_QUOTES | ENT_HTML5);
            },
            $text);
    }

    public function evaluate($expression) {
        $isError = false;

        $handler = function ($errno, $errStr, $errFile, $errLine)
        use (&$isError) {
            $isError = ! in_array($errno, [E_WARNING, E_NOTICE]);
        };

        $data = $this->getData();

        $oldHandler = set_error_handler($handler);

        if ($this->isTranslation($expression)) {
            return isset($this->translations[$expression]) ?
                $this->translations[$expression] : '';
        }

        try {
            $result = $this->evaluateSub($expression, $data);
        } catch (\Error $error) {
            throw new \RuntimeException(
                sprintf('Error: %s on evaluating expression %s',
                    $error->getMessage(), $expression));
        }

        set_error_handler($oldHandler);

        if ($isError) {
            throw new \RuntimeException("Error on evaluating: '$expression'");
        }

        return $result ?: '';
    }

    private function isTranslation($expression) {
        return preg_match('/^[_a-zA-Z][-_0-9a-zA-Z]*$/', $expression);
    }

    private function evaluateSub($expression_8slSL29x, $data_8slSL29x) {
        foreach ($data_8slSL29x as $key_8slSL29x => $value_8slSL29x) {
            ${ $key_8slSL29x } = $value_8slSL29x;
        }

        return eval('return ' . $expression_8slSL29x . ';');
    }

    public function addLayer($data = []) {
        $this->layers[] = $data;
    }

    public function removeLayer() {
        if (count($this->layers) == 1) {
            throw new \RuntimeException("can't remove last layer");
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

    private function getData() : array {
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
