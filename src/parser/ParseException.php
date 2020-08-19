<?php

namespace tplLib;

use \RuntimeException;

class ParseException extends RuntimeException {

    public $message;
    public $pos;

    public function __construct($message, $pos) {
        parent::__construct($message);

        $this->pos = $pos;
    }
}