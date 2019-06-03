<?php

class ParseException extends Exception {

    public $message;
    public $pos;

    public function __construct($message, $pos) {
        parent::__construct($message);

        $this->pos = $pos;
    }
}