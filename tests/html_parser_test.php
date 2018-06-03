<?php

require_once('ExtendedTextCase.php');
require_once('../src/parser/HtmlParser.php');
require_once('../src/parser/TreeBuilderActions.php');

class HtmlParserTests extends ExtendedTestCase {

    function _voidTag() {
        $input = '<input />';

        $tokens = (new HtmlLexer($input))->tokenize();

        (new HtmlParser($tokens, new TreeBuilderActions()))->parse();
    }

    function test1() {
        $input = '<html><p class="c" id="1" disabled>hello</p></html>';

        $tokens = (new HtmlLexer($input))->tokenize();

        (new HtmlParser($tokens))->parse();
    }

    function test2() {
        $input = '  <!-- c -->  <img>';

        $tokens = (new HtmlLexer($input))->tokenize();

//        print_r($tokens);

        (new HtmlParser($tokens))->parse();
    }

    function fromFile() {
        $input = join('', file('test-data/samples/abc.com.html'));

        $tokens = (new HtmlLexer($input))->tokenize();

        (new HtmlParser($tokens))->parse();
    }

}

!debug_backtrace() && (new HtmlParserTests())->run(new TextReporter());