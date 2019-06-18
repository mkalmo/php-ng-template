<?php

namespace tplLib;

class TextNode extends AbstractNode {

    private $text;

    public function __construct($text) {
        $this->text = $text;
    }

    public function render($scope) {
        return $scope->replaceCurlyExpression($this->text);
    }

}
