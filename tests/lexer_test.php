<?php

require_once('ExtendedTextCase.php');
require_once('../src/parser/ListLexer.php');
require_once('../src/parser/HtmlLexer.php');

class LexerTests extends ExtendedTestCase {

    function startTag() {
        $input = '<img src="index.php" disabled>';

        $tokens = (new HtmlLexer($input))->tokenize();

        print $this->tokensToString($tokens);
    }

    function endTag() {
        $input = '</p>';

        $tokens = (new HtmlLexer($input))->tokenize();

        print $this->tokensToString($tokens);
    }

    function htmlText() {
        $input = '"Hello"';

        $tokens = (new HtmlLexer($input))->tokenize();

        print $this->tokensToString($tokens);
    }

    function htmlComment() {
        $input = '<!-- c -->';

        $tokens = (new HtmlLexer($input))->tokenize();

        print $this->tokensToString($tokens);
    }

    function script() {
        $input = '<script> < </script>';

        $tokens = (new HtmlLexer($input))->tokenize();

        print $this->tokensToString($tokens);
    }

    function _fromFile() {
        $input = join('', file('test-data/samples/abc.com.html'));

        $tokens = (new HtmlLexer($input))->tokenize();

        print $this->tokensToString($tokens);
    }



    private function tokenTypes($tokens) {
        $types = [];
        foreach ($tokens as $token) {
            $types[] = $token->getType();
        }
        return $types;
    }

    private function tokensToString($tokens) {
        $types = [];
        foreach ($tokens as $token) {
            $types[] = sprintf('%s(%s)', $token->getType(), $token->getText());
        }
        return join(', ', $types);
    }

}

function toString($list) {
    return join(', ', $list);
}

(new LexerTests())->run(new TextReporter());