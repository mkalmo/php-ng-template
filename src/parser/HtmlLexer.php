<?php

require_once 'Token.php';

class HtmlLexer {

    private $p;
    private $c;
    private $input;
    private $tokens = [];

    const EOF_CHAR = '<EOF>';
    const EOF_TYPE = 'EOF_TYPE';

    const TAG_OPEN = 'TAG_OPEN';
    const TAG_CLOSE = 'TAG_CLOSE';
    const TAG_NAME = 'TAG_NAME';
    const TAG_SLASH = 'TAG_SLASH';
    const TAG_EQUALS = 'TAG_EQUALS';

    const HTML_TEXT = 'HTML_TEXT';
    const HTML_COMMENT = 'HTML_COMMENT';
    const SCRIPT = 'SCRIPT';
    const DTD = 'DTD';

    const DOUBLE_QUOTE_STRING = 'DOUBLE_QUOTE_STRING';
    const SINGLE_QUOTE_STRING = 'SINGLE_QUOTE_STRING';

    public function __construct($input) {
        $this->input = $input;
        $this->p = 0;
        $this->consume();
    }

    public function tokenize() {

        while ($this->c !== self::EOF_CHAR) {
            if ($this->isMatch('<!--')) {
                $this->HTML_COMMENT();
            } else if ($this->isMatch('<!')) {
                $this->DTD();
            } else if ($this->isMatch('<script')) {
                $this->SCRIPT();
            } else if ($this->c === '<') {
                $this->TAG();
            } else {
                $this->HTML_TEXT();
            }
        }

        $this->tokens[] = new Token(self::EOF_TYPE, '<EOF>');

        return $this->tokens;
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
        $contents = '';
        while ($this->c !== '>' && $this->c !== self::EOF_CHAR) {
            $contents .= $this->c;
            $this->consume();
        }

        $this->match('>');
        $this->tokens[] = new Token(self::DTD, $contents);
    }

    private function HTML_COMMENT() {
        $contents = '';
        while (!$this->isMatch('-->') && $this->c !== self::EOF_CHAR) {
            $contents .= $this->c;
            $this->consume();
        }

        if ($this->isMatch('-->')) {
            $contents .= '-->';
            $this->match('-');
            $this->match('-');
            $this->match('>');
        }

        $this->tokens[] = new Token(self::HTML_COMMENT, $contents);
    }

    private function SCRIPT() {
        $contents = '';
        while (!$this->isMatch('</script>') && $this->c !== self::EOF_CHAR) {
            $contents .= $this->c;
            $this->consume();
        }

        if ($this->isMatch('</script>')) {
            $contents .= '</script>';
            $this->match('<');
            $this->match('/');
            $this->match('s');
            $this->match('c');
            $this->match('r');
            $this->match('i');
            $this->match('p');
            $this->match('t');
            $this->match('>');
        }

        $this->tokens[] = new Token(self::SCRIPT, $contents);
    }

    private function TAG() {
        $this->match('<');
        $this->tokens[] = new Token(self::TAG_OPEN, '<');

        while ($this->c !== '>') {

            if ($this->c === self::EOF_CHAR) {
                throw new Error("tag started but not closed");
            }

            if ($this->isLETTER()) {
                $this->TAG_NAME();
            } else if ($this->c === '=') {
                $this->ATTVALUE();
            } else if ($this->c === '/') {
                $this->consume();
                $this->tokens[] = new Token(self::TAG_SLASH, '/');
            } else if ($this->isWS()) {
                $this->WS();
            } else {
                throw new Error("invalid character: " .
                    $this->c . $this->locationString());
            }
        }

        $this->match('>');
        $this->tokens[] = new Token(self::TAG_CLOSE, '>');
    }

    private function locationString() {
        $textParsed = substr($this->input, 0, $this->p);
        $lines = explode("\n", $textParsed);
        $lineNr = count($lines);
        $colNr = strlen($lines[$lineNr - 1]);

        return sprintf(' at Line: %s, Col: %s', $lineNr, $colNr);
    }

    private function ATTVALUE() {
        $this->match('=');
        $this->tokens[] = new Token(self::TAG_EQUALS, '=');

        if ($this->isWS()) {
            $this->WS();
        }

        $this->DOUBLE_QUOTE_STRING();
    }

    private function TAG_NAME() {
        $name = '';

        do {
            $name .= $this->c;
            $this->consume();
        } while ($this->isTAG_NAME_CHAR());

        $this->tokens[] = new Token(self::TAG_NAME, $name);
    }

    private function DOUBLE_QUOTE_STRING() {
        $this->match('"');
        $contents = '';
        while ($this->c !== '"') {

            if ($this->c === self::EOF_CHAR) {
                throw new Error("quoted string started but did not close");
            }

            $contents .= $this->c;
            $this->consume();
        }
        $this->match('"');

        $this->tokens[] = new Token(self::DOUBLE_QUOTE_STRING, $contents);
    }

    private function isWS() {
        return preg_match('/^\s$/', $this->c);
    }

    private function WS() {
        while ($this->isWS()) {
            $this->consume();
        }
    }

    public function match($x) {
        if ($this->c === $x) {
            $this->consume();
        } else {
            throw new Error("expecting: " . $x . "; found: " . $this->c . $this->locationString());
        }
    }

    public function consume() {
        if ($this->p >= strlen($this->input)) {
            $this->c = self::EOF_CHAR;
        } else {
            $this->c = substr($this->input, $this->p, 1);
            $this->p++;
        }
    }

    private function isMatch($stringToMatch) {
        for ($i = 0; $i < strlen($stringToMatch); $i++) {

            $inputCharPos = $this->p - 1 + $i;

            if ($inputCharPos >= strlen($this->input)) {
                return false;
            }

            $inputChar = substr($this->input, $inputCharPos, 1);
            $matchChar = substr($stringToMatch, $i, 1);

            if ($inputChar !== $matchChar) {
                return false;
            }
        }

        return true;
    }

    public function isLETTER() {
        return preg_match('/^[a-zA-Z]$/', $this->c);
    }

    public function isTAG_NAME_CHAR() {
        return preg_match('/^[-_\.:a-zA-Z0-9]$/', $this->c);
    }

}

