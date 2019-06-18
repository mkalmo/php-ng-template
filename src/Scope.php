<?php

namespace tplLib;

class Scope {
    private $layers = [];
    public $mainTemplatePath;

    public function __construct($data = [], $mainTemplatePath = null) {
        $this->addLayer($data);
        $this->mainTemplatePath = $mainTemplatePath;
    }

    public function replaceCurlyExpression($text) {
        $result = preg_replace_callback(
            '|{{(.+?)}}|im',
            function ($matches) {
                $result = $this->evaluate(trim($matches[1]));
                return htmlspecialchars($result, ENT_NOQUOTES);
            },
            $text);

        return $result;
    }

    function evaluate($expression) {
        $isError = false;

        $handler = function ($errno, $errstr, $errfile, $errline)
        use (&$isError) {
            $isError = $errno !== E_NOTICE;
        };

        $data = $this->getData();

        $oldHandler = set_error_handler($handler);

        try {
            $result = $this->evaluateSub($expression, $data);
        } catch (\Error $error) {
            throw new \Exception(
                "error evaluating: $expression; " .  $error->getMessage());
        }

        set_error_handler($oldHandler);

        if ($isError) {
            throw new \Exception("error evaluating: $expression");
        }

        return $result;
    }

    private function evaluateSub($expression_8slSL29x, $data_8slSL29x) {
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
