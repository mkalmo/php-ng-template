<?php

require_once '../src/Scope.php';

class TextNode extends Node {

    private $text;

    public function __construct($text) {
        $this->text = $text;
    }

    public function render($scope) {
        return $scope->replaceCurlyExpression($this->text);
    }

}
