<?php

namespace tplLib;

class Token {

    public $type;
    public $text;

    public function __construct($type, $text) {
        $this->type = $type;
        $this->text = $text;
    }
}
