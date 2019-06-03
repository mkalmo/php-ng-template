<?php

require_once('ExtendedTextCase.php');
require_once('../src/parser/ListLexer.php');
require_once('../src/parser/HtmlLexer.php');

class LexerTests extends ExtendedTestCase {

    function startTag() {
        $input = '<img src="index.php" disabled id=1>';

        $tokens = (new HtmlLexer($input))->tokenize();

        $this->assertListEqual([
            HtmlLexer::TAG_OPEN,
            HtmlLexer::TAG_NAME,
            HtmlLexer::TAG_NAME,
            HtmlLexer::TAG_EQUALS,
            HtmlLexer::DOUBLE_QUOTE_STRING,
            HtmlLexer::TAG_NAME,
            HtmlLexer::TAG_NAME,
            HtmlLexer::TAG_EQUALS,
            HtmlLexer::UNQUOTED_STRING,
            HtmlLexer::TAG_CLOSE

        ], self::tokenTypes($tokens));
    }

    function endTag() {
        $input = '</p>';

        $tokens = (new HtmlLexer($input))->tokenize();

        $this->assertListEqual([
            HtmlLexer::TAG_OPEN,
            HtmlLexer::TAG_SLASH,
            HtmlLexer::TAG_NAME,
            HtmlLexer::TAG_CLOSE

        ], $this->tokenTypes($tokens));
    }

    function selfCloseTag() {
        $input = '<br />';

        $tokens = (new HtmlLexer($input))->tokenize();

        $this->assertListEqual([
            HtmlLexer::TAG_OPEN,
            HtmlLexer::TAG_NAME,
            HtmlLexer::TAG_SLASH_CLOSE

        ], $this->tokenTypes($tokens));
    }

    function htmlText() {
        $input = '"Hello"';

        $tokens = (new HtmlLexer($input))->tokenize();

        $this->assertListEqual([
            HtmlLexer::HTML_TEXT

        ], $this->tokenTypes($tokens));

    }

    function htmlComment() {
        $input = '<!-- c -->';

        $tokens = (new HtmlLexer($input))->tokenize();

        $this->assertListEqual([
            HtmlLexer::HTML_COMMENT

        ], $this->tokenTypes($tokens));
    }

    function script() {
        $input = '<script> < </script>';

        $tokens = (new HtmlLexer($input))->tokenize();

        $this->assertListEqual([
            HtmlLexer::SCRIPT

        ], $this->tokenTypes($tokens));
    }

    function seaWs() {
        $input = ' text';

        $tokens = (new HtmlLexer($input))->tokenize();

        $this->assertListEqual([
            HtmlLexer::SEA_WS,
            HtmlLexer::HTML_TEXT

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

    function invalidSymbol() {
        $this->expectErrorAt(7, '<img><-');
    }

    private function expectErrorAt($pos, $input) {
        try {
            (new HtmlLexer($input))->tokenize();
        } catch (LexerException $e) {
            $this->assertEqual($pos, $e->pos);
            return;
        }

        throw new Error("unexpected pass");
    }

    private static function tokenTypes($tokens) {
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

!debug_backtrace() && (new LexerTests())->run(new TextReporter());