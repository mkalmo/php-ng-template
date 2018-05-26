<?php

require_once('ExtendedTextCase.php');
require_once('../src/parser/RegexLexer.php');

class LexerTests extends ExtendedTestCase {

    function createsTokensFromHtml() {
        $html = '<body><p>p1</p></body>';

        $tokens = (new RegexLexer())->tokenize($html);

        $this->assertEqual(5, count($tokens));

        $expected = ['body', 'p', '', 'p', 'body'];

        $this->assertEqual($expected, self::getNames($tokens));
    }

    static function getNames($tokens) {
        return array_map(function ($each) {
            return $each->getTagName();
        }, $tokens);
    }

}

(new LexerTests())->run(new TextReporter());