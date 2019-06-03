<?php

class LexerException extends Exception {

    public $message;
    public $pos;
    public $char;

    public function __construct($message, $pos, $char) {
        parent::__construct();

        $this->message = $message;
        $this->pos = $pos;
        $this->char = $char;
    }

}