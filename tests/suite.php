<?php

require_once(dirname(__FILE__) . '/../vendor/simpletest/simpletest/autorun.php');

class AllFileTests extends TestSuite {
    function __construct() {
        parent::__construct();

        $this->collect(dirname(__FILE__) . '/',
            new SimplePatternCollector('/_test.php/'));
    }
}

