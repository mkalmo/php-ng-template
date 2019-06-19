<?php

namespace tplLib;


class MiscNode extends AbstractNode {

    private $text;

    public function __construct($text) {
        $this->text = $text;
    }

    public function render($scope) {
        return $this->text;
    }

}