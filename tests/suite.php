<?php

require_once(__DIR__ . '/../vendor/simpletest/simpletest/autorun.php');

class AllFileTests extends TestSuite {
    function __construct() {
        parent::__construct();

        $this->collect(__DIR__ . '/',
            new SimplePatternCollector('/(lexer|scope)_test.php/'));
    }
}

