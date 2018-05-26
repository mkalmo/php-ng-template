<?php

require_once('common.php');
require_once('../src/concat.php');

class ConcatTests extends ExtendedTestCase {

    function concatenatesPhpFiles() {
        $contents = concatenatePhpFiles('../data/concat/main.php');

        $this->assertEqual(9, count(explode("\n", $contents)));

        $this->assertEqual(3, substr_count($contents, 'print'));
    }
}

(new ConcatTests())->run(new TextReporter());
