<?php

require_once('ExtendedTextCase.php');
require_once('../src/parser/FileParser.php');
require_once('../src/Scope.php');

use tplLib\FileParser;
use tplLib\Scope;

class BrokenFileTests extends ExtendedTestCase {


    function x_allFromFile() {
        foreach (new DirectoryIterator('test-data/samples') as $fileInfo) {
            if($fileInfo->isDot()) {
                continue;
            }

            $path = $fileInfo->getPathname();

            print($path . PHP_EOL);

            (new FileParser($path))->parse();
        }
    }

    function x_fromFile() {
        $path = 'test-data/tpl/main.html';

        $tree = (new FileParser($path))->parse();

        $scope = new Scope([], dirname($path));

        print $tree->render($scope);
    }

}

!debug_backtrace() && (new BrokenFileTests())->run(new TextReporter());