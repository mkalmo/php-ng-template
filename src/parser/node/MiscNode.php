<?php

namespace tplLib;


class MiscNode extends AbstractNode {

    private $text;

    public function __construct($text) {
        parent::__construct('');

        $this->text = $text;
    }

    public function render($scope) {
        return $this->text;
    }

}
