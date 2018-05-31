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

    const SEA_WS = 'SEA_WS';
    const HTML_TEXT = 'HTML_TEXT';
    const HTML_COMMENT = 'HTML_COMMENT';
    const SCRIPT = 'SCRIPT';
    const DTD = 'DTD';

    const DOUBLE_QUOTE_STRING = 'DOUBLE_QUOTE_STRING';
    const SINGLE_QUOTE_STRING = 'SINGLE_QUOTE_STRING';

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
            } else if ($this->isMatch('<script')) {
                $this->SCRIPT();
            } else if ($this->c === '<') {
                $this->TAG();
            }  else if ($this->isWS()) {
                $this->SEA_WS();
            } else {
                $this->HTML_TEXT();
            }
        }

        $this->tokens[] = new Token(self::EOF_TYPE, '<EOF>');

        return $this->tokens;
    }

    private function SEA_WS() {
        $contents = '';
        while ($this->isWS()) {
            $contents .= $this->c;
            $this->consume();
        }

        $this->tokens[] = new Token(self::SEA_WS, $contents);
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

        return sprintf(' at Line: %s: %s', $lineNr, $colNr);
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
            throw new Error('unexpected attr value');
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

    private function isWS() {
        return $this->c === " "
            || $this->c === "\t"
            || $this->c === "\r"
            || $this->c === "\n";
    }

    private function WS() {
        while ($this->isWS()) {
            $this->consume();
        }
    }

    public function match($stringToMatch) {
        foreach (str_split($stringToMatch) as $char) {
            if ($this->c === $char) {
                $this->consume();
            } else {
                throw new Error(sprintf('expecting: %s; found: %s %s',
                    $char, $this->c, $this->locationString()));
            }
        }

        return  $stringToMatch;
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

