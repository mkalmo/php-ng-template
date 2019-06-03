<?php

require_once('ExtendedTextCase.php');
require_once('../src/parser/FileParser.php');

class BrokenFileTests extends ExtendedTestCase {

    function _allFromFile() {
        foreach (new DirectoryIterator('test-data/samples') as $fileInfo) {
            if($fileInfo->isDot()) {
                continue;
            }

            $path = $fileInfo->getPathname();

            print($path . PHP_EOL);

            (new FileParser($path))->parse();
        }
    }

    function fromFile() {
        $path = 'test-data/simple/a.html';

        (new FileParser($path))->parse();
    }



}

!debug_backtrace() && (new BrokenFileTests())->run(new TextReporter());