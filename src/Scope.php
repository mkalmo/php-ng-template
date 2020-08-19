<?php

namespace tplLib;

use \RuntimeException;
use \Error;

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
                return htmlspecialchars($result, ENT_NOQUOTES);
            },
            $text);
    }

    public function evaluate($expression) {
        $isError = false;

        $handler = function ($errno, $errstr, $errfile, $errline)
        use (&$isError) {
            $isError = $errno !== E_NOTICE;
        };

        $data = $this->getData();

        $oldHandler = set_error_handler($handler);

        if ($this->isTranslation($expression)) {
            return isset($this->translations[$expression]) ?
                $this->translations[$expression] : '';
        }

        try {
            $result = $this->evaluateSub($expression, $data);
        } catch (Error $error) {
            $this->throwEvaluateException($expression);
        }

        set_error_handler($oldHandler);

        if ($isError) {
            $this->throwEvaluateException($expression);
        }

        return $result;
    }

    private function throwEvaluateException($expression) {
        throw new RuntimeException("error evaluating: '$expression'");
    }

    private function isTranslation($expression) {
        return preg_match('/^[_a-zA-Z][-_0-9a-zA-Z]*$/', $expression);
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
            throw new RuntimeException("can't remove last layer");
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
