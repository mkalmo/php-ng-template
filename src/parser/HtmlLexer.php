<?php

require_once 'Token.php';
require_once 'ParseException.php';

class HtmlLexer {

    private $p;
    private $c;
    private $input;
    private $tokens = [];

    const EOF_CHAR = '<EOF>';
    const EOF_TYPE = 'EOF_TYPE';

    const TAG_OPEN = 'TAG_OPEN';
    const TAG_CLOSE = 'TAG_CLOSE';
    const TAG_SLASH_CLOSE = 'TAG_SLASH_CLOSE';
    const TAG_NAME = 'TAG_NAME';
    const TAG_SLASH = 'TAG_SLASH';
    const TAG_EQUALS = 'TAG_EQUALS';

    const WS = 'WS';
    const HTML_TEXT = 'HTML_TEXT';
    const HTML_COMMENT = 'HTML_COMMENT';
    const SCRIPT = 'SCRIPT';
    const DTD = 'DTD';
    const XML_DECLARATION = 'XML_DECLARATION';

    const DOUBLE_QUOTE_STRING = 'DOUBLE_QUOTE_STRING';
    const SINGLE_QUOTE_STRING = 'SINGLE_QUOTE_STRING';
    const UNQUOTED_STRING = 'UNQUOTED_STRING';

    public function __construct($input) {
        $this->input = $input;
        $this->p = -1;
        $this->consume();
    }

    public function tokenize() {

        while ($this->c !== self::EOF_CHAR) {
            if ($this->isMatch('<!--')) {
                $this->HTML_COMMENT();
            } else if ($this->isMatch('<!')) {
                $this->DTD();
            } else if ($this->isMatch('<?xml')) {
                $this->XML_DECLARATION();
            } else if ($this->isMatch('<script')) {
                $this->SCRIPT();
            } else if ($this->c === '<') {
                $this->TAG();
            }  else if ($this->isWS()) {
                $this->WS();
            } else {
                $this->HTML_TEXT();
            }
        }

        return $this->tokens;
    }

    private function isWS() {
        return $this->c === " "
            || $this->c === "\t"
            || $this->c === "\r"
            || $this->c === "\n";
    }

    private function WS() {
        $contents = '';
        while ($this->isWS()) {
            $contents .= $this->c;
            $this->consume();
        }

        $this->tokens[] = new Token(self::WS, $contents);
    }

    private function HTML_TEXT() {
        $contents = '';
        while ($this->c !== '<' && $this->c !== self::EOF_CHAR) {
            $contents .= $this->c;
            $this->consume();
        }

        $this->tokens[] = new Token(self::HTML_TEXT, $contents);
    }

    private function DTD() {
        $contents = $this->matchBetweenStrings('<!', '>');
        $this->tokens[] = new Token(self::DTD, $contents);
    }

    private function XML_DECLARATION() {
        $contents = $this->matchBetweenStrings('<?xml', '>');
        $this->tokens[] = new Token(self::DTD, $contents);
    }

    private function HTML_COMMENT() {
        $contents = $this->matchBetweenStrings('<!--', '-->');
        $this->tokens[] = new Token(self::HTML_COMMENT, $contents);
    }

    private function SCRIPT() {
        $contents = $this->matchBetweenStrings('<script', '</script>');
        $this->tokens[] = new Token(self::SCRIPT, $contents);
    }

    private function TAG() {
        $this->match('<');
        $this->tokens[] = new Token(self::TAG_OPEN, '<');

        while ($this->c !== '>') {

            if ($this->c === self::EOF_CHAR) {
                $this->throwException("tag started but not closed");
            }

            if ($this->isLETTER()) {
                $this->TAG_NAME();
            } else if ($this->c === '=') {
                $this->ATTVALUE();
            } else if ($this->isMatch('/>')) {
                $this->match('/>');
                $this->tokens[] = new Token(self::TAG_SLASH_CLOSE, '/>');
                return;
            } else if ($this->c === '/') {
                $this->consume();
                $this->tokens[] = new Token(self::TAG_SLASH, '/');
            } else if ($this->isWS()) {
                $this->WS();
            } else {
                $this->throwException(sprintf('invalid character: %s', $this->c));
            }
        }

        $this->match('>');
        $this->tokens[] = new Token(self::TAG_CLOSE, '>');
    }

    private function ATTVALUE() {
        $this->match('=');
        $this->tokens[] = new Token(self::TAG_EQUALS, '=');

        if ($this->isWS()) {
            $this->WS();
        }

        if ($this->c === "'") {
            $this->SINGLE_QUOTE_STRING();
        } else if ($this->c === '"') {
            $this->DOUBLE_QUOTE_STRING();
        } else {
            $this->UNQUOTED_STRING();
        }
    }

    private function TAG_NAME() {
        $name = '';

        do {
            $name .= $this->c;
            $this->consume();
        } while ($this->isTAG_NAME_CHAR());

        $this->tokens[] = new Token(self::TAG_NAME, $name);
    }

    private function SINGLE_QUOTE_STRING() {
        $contents = $this->matchBetweenStrings("'", "'");
        $this->tokens[] = new Token(self::SINGLE_QUOTE_STRING, $contents);
    }

    private function DOUBLE_QUOTE_STRING() {
        $contents = $this->matchBetweenStrings('"', '"');
        $this->tokens[] = new Token(self::DOUBLE_QUOTE_STRING, $contents);
    }

    private function UNQUOTED_STRING() {
        $contents = '';
        while (!$this->isWS()
            && $this->c !== '>'
            && $this->c !== self::EOF_CHAR) {

            $contents .= $this->c;
            $this->consume();
        }

        $this->tokens[] = new Token(self::UNQUOTED_STRING, $contents);
    }

    public function match($stringToMatch) {
        foreach (str_split($stringToMatch) as $char) {
            if ($this->c === $char) {
                $this->consume();
            } else {
                $message = sprintf(
                    'expecting: %s but found: %s', $char, $this->c);
                $this->throwException($message);
            }
        }

        return  $stringToMatch;
    }

    private function throwException($message) {
        throw new ParseException(
            $message,
            $this->p);
    }

    public function consume() {
        $this->p++;
        $this->c = $this->charFromPos($this->p);
    }

    private function charFromPos($pos) {
        return $pos >= strlen($this->input)
            ? self::EOF_CHAR
            : substr($this->input, $pos, 1);

    }

    private function isMatch($stringToMatch) {
        $p = $this->p;

        foreach (str_split($stringToMatch) as $char) {

            if ($char !== $this->charFromPos($p)) {
                return false;
            }

            $p++;
        }

        return true;
    }

    public function isLETTER() {
        return ctype_alpha($this->c);
    }

    public function isTAG_NAME_CHAR() {
        return preg_match('/^[-_\.:a-zA-Z0-9]$/', $this->c);
    }

    private function matchBetweenStrings($start, $end) {
        $contents = $this->match($start);

        while (!$this->isMatch($end) && $this->c !== self::EOF_CHAR) {
            $contents .= $this->c;
            $this->consume();
        }

        return $contents . $this->match($end);
    }
}

