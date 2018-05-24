<?php

class RootStartTag extends Token {

    public function getTagName() {
        return null;
    }

    public function __construct() {
        parent::__construct('<root>');
    }
}
