<?php

require_once('ExtendedTextCase.php');
require_once('../src/parser/ListLexer.php');
require_once('../src/parser/HtmlLexer.php');

class LexerTests extends ExtendedTestCase {

    function startTag() {
        $input = '<img src="index.php" disabled>';

        $tokens = (new HtmlLexer($input))->tokenize();

        $this->assertListEqual([
            HtmlLexer::TAG_OPEN,
            HtmlLexer::TAG_NAME,
            HtmlLexer::TAG_NAME,
            HtmlLexer::TAG_EQUALS,
            HtmlLexer::DOUBLE_QUOTE_STRING,
            HtmlLexer::TAG_NAME,
            HtmlLexer::TAG_CLOSE,
            HtmlLexer::EOF_TYPE

        ], $this->tokenTypes($tokens));
    }

    function endTag() {
        $input = '</p>';

        $tokens = (new HtmlLexer($input))->tokenize();

        $this->assertListEqual([
            HtmlLexer::TAG_OPEN,
            HtmlLexer::TAG_SLASH,
            HtmlLexer::TAG_NAME,
            HtmlLexer::TAG_CLOSE,
            HtmlLexer::EOF_TYPE

        ], $this->tokenTypes($tokens));
    }

    function htmlText() {
        $input = '"Hello"';

        $tokens = (new HtmlLexer($input))->tokenize();

        $this->assertListEqual([
            HtmlLexer::HTML_TEXT,
            HtmlLexer::EOF_TYPE

        ], $this->tokenTypes($tokens));

    }

    function htmlComment() {
        $input = '<!-- c -->';

        $tokens = (new HtmlLexer($input))->tokenize();

        $this->assertListEqual([
            HtmlLexer::HTML_COMMENT,
            HtmlLexer::EOF_TYPE

        ], $this->tokenTypes($tokens));
    }

    function script() {
        $input = '<script> < </script>';

        $tokens = (new HtmlLexer($input))->tokenize();

        $this->assertListEqual([
            HtmlLexer::SCRIPT,
            HtmlLexer::EOF_TYPE

        ], $this->tokenTypes($tokens));
    }

    function seaWs() {
        $input = ' text';

        $tokens = (new HtmlLexer($input))->tokenize();

        $this->assertListEqual([
            HtmlLexer::SEA_WS,
            HtmlLexer::HTML_TEXT,
            HtmlLexer::EOF_TYPE

        ], $this->tokenTypes($tokens));
    }

    function x_fromFile() {
        $input = join('', file('test-data/samples/abc.com.html'));

        $start = microtime(true);

        for ($i = 0; $i < 10; $i++) {
            $tokens = (new HtmlLexer($input))->tokenize();
        }

        $elapsed = microtime(true) - $start;

        var_dump($elapsed / 10);  // 0.015

//        print $this->tokensToString($tokens);
    }

    private function tokenTypes($tokens) {
        $types = [];
        foreach ($tokens as $token) {
            $types[] = $token->type;
        }
        return $types;
    }

    private function tokensToString($tokens) {
        $types = [];
        foreach ($tokens as $token) {
            $types[] = sprintf('%s(%s)', $token->type, $token->text);
        }
        return join(', ', $types);
    }

}

(new LexerTests())->run(new TextReporter());