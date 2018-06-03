<?php

require_once 'node/RootNode.php';
require_once 'node/TagNode.php';
require_once 'node/VoidTagNode.php';
require_once 'node/TextNode.php';
require_once 'node/MiscNode.php';
require_once 'node/WsNode.php';
require_once 'node/IfNode.php';
require_once 'node/ForNode.php';

class TreeBuilderActions {

    private $stack;

    public function __construct() {
        $this->stack = [];
        $this->stack[] = new RootNode();
    }

    public function getResult() {
        list ($first) = $this->stack;
        return $first;
    }

    private function currentNode() {
        return $this->stack[count($this->stack) - 1];
    }

    public function tagStartAction($tagName, $attributes) {

        if (isset($attributes['tpl-if'])) {
            $node = new IfNode($tagName, $attributes);
        } else if (isset($attributes['tpl-foreach'])) {
            $node = new ForNode($tagName, $attributes);
        } else {
            $node = new TagNode($tagName, $attributes);
        }

        $this->currentNode()->addChild($node);

        $this->stack[] = $node;
    }

    public function tagEndAction($tagName) {
        array_pop($this->stack);
    }

    public function voidTagAction($tagName, $attributes, $hasSlashClose) {
        $node = new VoidTagNode($tagName, $attributes, $hasSlashClose);
        $this->currentNode()->addChild($node);
    }

    public function staticElementAction($token) {

        if ($token->type === HtmlLexer::HTML_TEXT) {
            $node = new TextNode($token->text);
        } else if ($token->type === HtmlLexer::SEA_WS) {
            $node = new WsNode($token->text);
        } else {
            $node = new MiscNode($token->text);
        }

        $this->currentNode()->addChild($node);
    }
}

