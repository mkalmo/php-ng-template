<?php

require_once('ExtendedTextCase.php');
require_once('../src/parser/HtmlParser.php');

class HtmlParserTests extends ExtendedTestCase {

    function test1() {
        $input = '<html><p class="c" id="1" disabled>hello</p></html>';

        $tokens = (new HtmlLexer($input))->tokenize();

        (new HtmlParser($tokens))->parse();
    }

    function _fromFile() {
        $input = join('', file('test-data/samples/abc.com.html'));

        $tokens = (new HtmlLexer($input))->tokenize();

        (new HtmlParser($tokens))->parse();
    }

}

(new HtmlParserTests())->run(new TextReporter());