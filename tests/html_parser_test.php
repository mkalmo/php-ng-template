<?php

require_once('ExtendedTextCase.php');
require_once('../src/parser/HtmlParser.php');
require_once('../src/parser/TreeBuilderActions.php');
require_once('../src/parser/DebugActions.php');

class HtmlParserTests extends ExtendedTestCase {

    function voidTag() {
        $input = '<input />';

        $tokens = (new HtmlLexer($input))->tokenize();

        (new HtmlParser($tokens))->parse();
    }

    function differentAttributeValues() {
        $input = '<p class="c" disabled id=1>hello</p>';

        $tokens = (new HtmlLexer($input))->tokenize();

        (new HtmlParser($tokens))->parse();
    }

    function _unclosedTag() {
        $input = '<html><a></html>';

        $this->expectErrorAt(11, $input);
    }

    private function expectErrorAt($pos, $input) {
        try {
            $tokens = (new HtmlLexer($input))->tokenize();

            (new HtmlParser($tokens))->parse();

        } catch (ParseException $e) {
            $this->assertEqual($pos, $e->pos);
            return;
        }

        throw new Error("unexpected pass");
    }
}

!debug_backtrace() && (new HtmlParserTests())->run(new TextReporter());