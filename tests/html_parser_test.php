<?php

require_once('ExtendedTextCase.php');
require_once('../src/parser/HtmlParser.php');
require_once('../src/parser/TreeBuilderActions.php');
require_once('../src/parser/DebugActions.php');

class HtmlParserTests extends ExtendedTestCase {

    function voidTag() {
        $input = '<input />';

        $tokens = (new HtmlLexer($input))->tokenize();

        (new HtmlParser($tokens, new TreeBuilderActions()))->parse();
    }

    function test1() {
        $input = '<html><p class="c" id="1" disabled>hello</p></html>';

        $tokens = (new HtmlLexer($input))->tokenize();

        (new HtmlParser($tokens))->parse();
    }

    function unclosedTag() {
        $input = '<html><a></html>';

        $tokens = (new HtmlLexer($input))->tokenize();

        (new HtmlParser($tokens))->parse();
    }

    function test2() {
        $input = '  <!-- c -->  <img>';

        $tokens = (new HtmlLexer($input))->tokenize();

//        print_r($tokens);

        (new HtmlParser($tokens))->parse();
    }

    function _allFromFile() {
        foreach (new DirectoryIterator('test-data/samples') as $fileInfo) {
            if($fileInfo->isDot()) {
                continue;
            }

            $path = $fileInfo->getPathname();
            $path = $fileInfo->getPathname();

            print($path . PHP_EOL);

            $input = join('', file($path));

            $tokens = (new HtmlLexer($input))->tokenize();

            (new HtmlParser($tokens, new TreeBuilderActions()))->parse();
        }
    }

    function _fromFile() {
        $path = 'test-data/samples/uglylink.html';

        print($path . PHP_EOL);

        $input = join('', file($path));

        $tokens = (new HtmlLexer($input))->tokenize();

//        print_r($tokens);

        (new HtmlParser($tokens, new DebugActions()))->parse();
    }



}

!debug_backtrace() && (new HtmlParserTests())->run(new TextReporter());