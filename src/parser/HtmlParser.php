<?php

require_once 'Token.php';
require_once('HtmlLexer.php');

class HtmlParser {

    private $p;
    private $input = [];
    private $actions;


    public function __construct($input, $actions) {
        $this->input = $input;
        $this->actions = $actions;
        $this->p = 0;
    }

    private function htmlDocument() {
        // htmlDocument
        //    : SEA_WS? xml? SEA_WS? dtd? SEA_WS? htmlElements*

        $this->optionalElement(HtmlLexer::SEA_WS);
        $this->optionalElement(HtmlLexer::DTD);
        $this->optionalElement(HtmlLexer::SEA_WS);

        while ($this->isHtmlElements()) {
            $this->htmlElements();
        }
    }

    private function htmlElements() {
        // htmlElements : htmlMisc* htmlElement htmlMisc*

        while ($this->isHtmlMisc()) {
            $this->htmlMisc();
        }

        $this->htmlElement();

        while ($this->isHtmlMisc()) {
            $this->htmlMisc();
        }
    }

    private function htmlElement() {
        // htmlElement : TAG_OPEN .. | script | style;

        if ($this->ltt() === HtmlLexer::SCRIPT) {
            $this->actions->staticElementAction($this->lt());
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

        if ($this->isVoidTag($tagName)) {
            $hasSlashClose = false;
            if ($this->ltt() === HtmlLexer::TAG_SLASH_CLOSE) {
                $hasSlashClose = true;
                $this->consume();
            } else {
                $this->match(HtmlLexer::TAG_CLOSE);
            }

            $this->actions->voidTagAction($tagName, $attributes, $hasSlashClose);
            return;
        }

        $this->match(HtmlLexer::TAG_CLOSE);

        $this->actions->tagStartAction($tagName, $attributes);

        $this->htmlContent();

        $this->match(HtmlLexer::TAG_OPEN);
        $this->match(HtmlLexer::TAG_SLASH);
        $this->match(HtmlLexer::TAG_NAME);
        $this->match(HtmlLexer::TAG_CLOSE);
        $this->actions->tagEndAction($tagName);
    }

    private function htmlContent() {
        // htmlContent : htmlChardata? ((htmlElement | xhtmlCDATA | htmlComment) htmlChardata?)*

        if ($this->isHtmlChardata()) {
            $this->htmlChardata();
        }

        while ($this->isHtmlElement() || $this->ltt() === HtmlLexer::HTML_COMMENT) {

            if ($this->ltt() === HtmlLexer::HTML_COMMENT) {
                $this->consume();
            } else if ($this->isHtmlElement()) {
                $this->htmlElement();
            } else {
                throw new Error();
            }

            if ($this->isHtmlChardata()) {
                $this->htmlChardata();
            }
        }
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
        $p = $this->p + $lookahead - 1;

        return $p < count($this->input) ? $this->input[$p] : null;
    }

    private function ltt($lookahead = 1) {
        $token = $this->lt($lookahead);

        return $token === null ? 'EOF_TYPE' : $token->type;
    }

    private function htmlMisc() {
        // htmlMisc : htmlComment | SEA_WS;

        $this->optionalElement(HtmlLexer::HTML_COMMENT);
        $this->optionalElement(HtmlLexer::SEA_WS);
    }

    private function isHtmlMisc() {
        return $this->ltt() === HtmlLexer::HTML_COMMENT
            || $this->ltt() === HtmlLexer::SEA_WS;
    }

    private function htmlAttribute() {
        // htmlAttribute
        //    : htmlAttributeName TAG_EQUALS htmlAttributeValue
        //    | htmlAttributeName
        //    ;

        $key = $this->lt()->text;
        $this->match(HtmlLexer::TAG_NAME);

        if ($this->ltt() === HtmlLexer::TAG_EQUALS) {
            $this->consume();

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

        $this->optionalElement(HtmlLexer::SEA_WS);
        $this->optionalElement(HtmlLexer::HTML_TEXT);
    }

    private function isHtmlChardata() {
        return $this->ltt() === HtmlLexer::HTML_TEXT
            || $this->ltt() === HtmlLexer::SEA_WS;
    }

    private function isVoidTag($name) {
        $voidTags = 'area base br col embed hr img input'
                  . 'keygen link meta param source track wbr';

        return in_array($name, explode(' ', $voidTags));
    }

    private function isHtmlElement() {
        if ($this->ltt() === HtmlLexer::TAG_OPEN
            && $this->ltt(2) === HtmlLexer::TAG_NAME) {

            return true;
        }

        return $this->ltt() === HtmlLexer::SCRIPT;
    }

    private function isHtmlElements() {
        return $this->isHtmlMisc() || $this->isHtmlElement();
    }

    private function optionalElement($tokenType) {
        if ($this->ltt() === $tokenType) {
            $this->actions->staticElementAction($this->lt());
            $this->consume();
        }
    }

    public function parse() {
        $this->htmlDocument();
    }

    public function consume() {
        $this->p++;
    }

}

