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

    function _differentAttributeValues() {
        $input = '<p class="c" disabled id=1>hello</p>';

        $tokens = (new HtmlLexer($input))->tokenize();

        (new HtmlParser($tokens))->parse();
    }

    function unclosedTag() {
        $input = '<html><a></html>';

        $tokens = (new HtmlLexer($input))->tokenize();

        (new HtmlParser($tokens))->parse();
    }

    function aaa() {
        $input = "<a>N 'ignored'<img src='1.gif' class=VideoIconBig> </a>";

        $tokens = (new HtmlLexer($input))->tokenize();

        (new HtmlParser($tokens))->parse();
    }

    function allFromFile() {
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

    function fromFile() {
        $path = 'test-data/samples/aljazeera.com.html';
        $path = 'test-data/broken/youtube.html';

        print($path . PHP_EOL);

        $input = join('', file($path));

        $tokens = (new HtmlLexer($input))->tokenize();

//        print_r($tokens);

//        (new HtmlParser($tokens, new DebugActions()))->parse();
    }



}

!debug_backtrace() && (new HtmlParserTests())->run(new TextReporter());