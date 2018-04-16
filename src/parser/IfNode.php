<?php

require_once '../src/Scope.php';

class IfNode extends Node {

    private $startTag;

    public function __construct($startTag) {
        $this->startTag = $startTag;
    }

    public function render($scope) {
        if (!$scope->evaluate($this->getExpression())) {
            return '';
        }

        $this->startTag = $this->removeTplAttributes($this->startTag);

        return parent::render($scope);
    }

    private function removeTplAttributes($text) {
        return preg_replace("/\s+tpl-if\w*=[\"'](.*)[\"']/", '', $text);
    }

    private function getExpression() {
        preg_match("/tpl-if\w*=[\"'](.*)[\"']/", $this->startTag, $matches);

        list ($whole_match, $expression) = $matches;

        return $expression;
    }
}
