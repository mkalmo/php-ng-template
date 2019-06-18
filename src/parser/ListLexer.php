<?php

require_once 'Token.php';

class ListLexer {

    private $p;
    private $c;
    private $input;

    const L_ANGLE = 'L_ANGLE';
    const R_ANGLE = 'R_ANGLE';
    const EQUALS = 'EQUALS';
    const EOF = 'EOF';
    const NAME = 'NAME';
    const COMMA = 'COMMA';
    const LBRACK = 'LBRACK';
    const RBRACK = 'RBRACK';
    const EOF_TYPE = 'EOF_TYPE';

    public function __construct($input) {
        $this->input = $input;
        $this->p = 0;
        $this->consume();
    }

    public function tokenize() {
        $result = [];

        do {
            $token = $this->nextToken();
            $result[] = $token;
        } while ($token->getType() !== self::EOF_TYPE);

        return $result;
    }

    public function consume() {
        if ($this->p >= strlen($this->input)) {
            $this->c = self::EOF;
        } else {
            $this->c = substr($this->input, $this->p, 1);
            $this->p++;
        }
    }

    public function isLETTER() {
        return preg_match('/^[a-zA-Z]$/', $this->c);
    }

    public function nextToken() {
        while ($this->c !== self::EOF) {
            $c = $this->c;

            if ($this->isWS()) {
                $this->WS();
            } else if ($c === ',') {
                $this->consume();
                return new Token(self::COMMA, $c);
            } else if ($c === '[') {
                $this->consume();
                return new Token(self::LBRACK, $c);
            } else if ($c === ']') {
                $this->consume();
                return new Token(self::RBRACK, $c);
            } else if ($this->isLETTER()) {
                return $this->NAME();
            } else {
                throw new Error("invalid character: " . $this->c);
            }
        }

        return new Token(self::EOF_TYPE, '<EOF>');
    }

    private function NAME() {
        $name = '';
        do {
            $name .= $this->c;
            $this->consume();
        } while ($this->isLETTER());

        return new Token(self::NAME, $name);
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
            throw new Error("expecting " . $x . "; found " . $this->c);
        }
    }
}

