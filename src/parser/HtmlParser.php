<?php

require_once 'Token.php';
require_once('HtmlLexer.php');

class HtmlParser {

    private $p;
    private $level = 0;
    private $input = [];

    public function __construct($input) {
        if (!is_array($input) || end($input)->type !== HtmlLexer::EOF_TYPE) {
            throw new Error('input does not end with EOF_TYPE token.');
        }

        $this->input = $input;
        $this->p = -1;
    }

    private function htmlDocument() {
        // htmlDocument
        //    : SEA_WS? xml? SEA_WS? dtd? SEA_WS? htmlElements*

        var_dump($this->ltt());

        if ($this->ltt() === HtmlLexer::DTD) {
            $this->consume();
        }

        while ($this->isHtmlElement()) {
            $this->htmlElements();
        }
    }

    private function htmlElements() {
        // htmlElements : htmlMisc* htmlElement htmlMisc*

        while ($this->ltt() === HtmlLexer::HTML_COMMENT) {
            $this->consume();
        }

        $this->htmlElement();

        while ($this->ltt() === HtmlLexer::HTML_COMMENT) {
            $this->consume();
        }


    }

    private function htmlElement() {
        // htmlElement : TAG_OPEN .. | script | style;

        if ($this->ltt() === HtmlLexer::SCRIPT) {
            $this->htmlElementAction($this->lt());
            $this->consume();
            return;
        }

        $this->match(HtmlLexer::TAG_OPEN);
        $tagName = $this->lt()->text;
        $this->match(HtmlLexer::TAG_NAME);

        $attributes = [];
        while ($this->ltt() === HtmlLexer::TAG_NAME) {
            list ($key, $value) = $this->htmlAttribute();
            $attributes[$key] = $value;
        }

        $this->match(HtmlLexer::TAG_CLOSE);
        $this->tagStartAction($tagName, $attributes);

        $this->htmlContent();

        $this->match(HtmlLexer::TAG_OPEN);
        $this->match(HtmlLexer::TAG_SLASH);
        $this->match(HtmlLexer::TAG_NAME);
        $this->match(HtmlLexer::TAG_CLOSE);
        $this->tagEndAction($tagName);
    }

    private function htmlContent() {
        // htmlContent : htmlChardata? ((htmlElement | xhtmlCDATA | htmlComment) htmlChardata?)*

        if ($this->isHtmlChardata()) {
            $this->htmlChardata();
        }

        while ($this->isHtmlElement()) {
            $this->htmlElement();

            if ($this->isHtmlChardata()) {
                $this->htmlChardata();
            }
        }

    }

    private function tagStartAction($tagName, $attributes) {
        $padding = str_repeat('  ', $this->level);
        printf('%s<%s%s>' . PHP_EOL,
            $padding, $tagName,
            $this->attributeString($attributes));
        $this->level++;
    }

    private function attributeString($attributes) {
        $result = '';
        foreach ($attributes as $key => $value) {
            $result .= ' ' . $key;
            $result .= $value !== null ? '=' . $value : '';
        }

        return $result;
    }

    private function tagEndAction($tagName) {
        $this->level--;
        $padding = str_repeat('  ', $this->level);
        printf('%s</%s>' . PHP_EOL, $padding, $tagName);
    }

    private function match($type) {
        if ($this->ltt() === $type) {
            $this->consume();
        } else {
            throw new Error(sprintf(
                'expected: %s found: %s', $type, $this->ltt()));
        }
    }

    private function lt($lookahead = 1) {
        return $this->input[$this->p + $lookahead];
    }

    private function ltt($lookahead = 1) {
        return $this->lt($lookahead)->type;
    }

    private function htmlMisc() {
        // htmlMisc : htmlComment | SEA_WS;
    }

    private function htmlAttribute() {
        // htmlAttribute
        //    : htmlAttributeName TAG_EQUALS htmlAttributeValue
        //    | htmlAttributeName
        //    ;

        $key = $this->lt()->text;
        $this->match(HtmlLexer::TAG_NAME);

        if ($this->ltt() === HtmlLexer::TAG_EQUALS) {
            $this->match(HtmlLexer::TAG_EQUALS);
            if ($this->ltt() === HtmlLexer::DOUBLE_QUOTE_STRING) {
                $value = $this->lt()->text;
                $this->match(HtmlLexer::DOUBLE_QUOTE_STRING);
            } else if ($this->ltt() === HtmlLexer::SINGLE_QUOTE_STRING) {
                $value = $this->lt()->text;
                $this->match(HtmlLexer::SINGLE_QUOTE_STRING);
            } else {
                throw new Error('unexpected token: ' . $this->ltt());
            }

            return [$key, $value];

        } else {
            return [$key, null];
        }
    }

    private function htmlChardata() {
        // htmlChardata : HTML_TEXT | SEA_WS;

        $this->match(HtmlLexer::HTML_TEXT);
    }

    private function isHtmlChardata() {
        return $this->ltt() === HtmlLexer::HTML_TEXT;
    }

    private function isVoidTag($name) {
        return $name === 'br'
            || $name === 'img';
    }

    private function isHtmlElement() {
        if ($this->ltt() === HtmlLexer::TAG_OPEN
            && $this->ltt(2) === HtmlLexer::TAG_NAME) {

            return true;
        }

        return $this->ltt() === HtmlLexer::SCRIPT;
    }

    private function isHtmlElements() {
        $c = $this->c;

        return $c === HtmlLexer::TAG_OPEN;
    }

    public function parse() {
        $this->htmlDocument();
    }

    public function consume() {
        $this->p++;
    }

}

