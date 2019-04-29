<?php

require_once('../src/helpers.php');
require_once('../src/tpl.php');

class UtilTests extends ExtendedTestCase {

    function elementExists_returnsIt() {
        $found = array_find([1, 2, 3], function ($element) {
            return $element == 2;
        });

        $this->assertEqual(2, $found);
    }

    function elementDoesNotExist_returnNULL() {
        $found = array_find([1, 2, 3], function ($element) {
            return $element == 4;
        });

        $this->assertNull($found);
    }

    function findsMoreThanOne_throw() {
        $this->expectException(UnexpectedValueException::class);

        array_find([1, 2, 3, 2], function ($element) {
            return $element == 2;
        });
    }
}
