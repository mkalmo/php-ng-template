<?php

namespace tplLib;

require_once 'node/RootNode.php';
require_once 'node/TagNode.php';
require_once 'node/TextNode.php';
require_once 'node/MiscNode.php';
require_once 'node/WsNode.php';
require_once 'node/IfNode.php';
require_once 'node/ForNode.php';
require_once 'node/IncludeNode.php';

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
        $node = $this->createTag($tagName, $attributes);

        $this->currentNode()->addChild($node);

        $this->stack[] = $node;
    }

    private function createTag($tagName, $attributes) {
        if (isset($attributes['tpl-if'])) {
            return new IfNode($tagName, $attributes);
        } else if (isset($attributes['tpl-foreach'])) {
            return new ForNode($tagName, $attributes);
        } else if (isset($attributes['tpl-include'])) {
            return new IncludeNode($tagName, $attributes);
        } else {
            return new TagNode($tagName, $attributes);
        }
    }

    public function tagEndAction($tagName) {
        array_pop($this->stack);
    }

    public function voidTagAction($tagName, $attributes, $hasSlashClose) {
        $node = $this->createTag($tagName, $attributes);

        $node->makeVoid();

        if ($hasSlashClose) {
            $node->addSlashClose();
        }

        $this->currentNode()->addChild($node);
    }

    public function staticElementAction($token) {

        if ($token->type === HtmlLexer::HTML_TEXT) {
            $wholeText = $token->text;
            $trimmed = rtrim($wholeText);
            $whiteSpace = substr($wholeText, strlen($trimmed));

            $this->currentNode()->addChild(new TextNode($trimmed));
            if (!empty($whiteSpace)) {
                $this->currentNode()->addChild(new WsNode($whiteSpace));
            }

        } else if ($token->type === HtmlLexer::SEA_WS) {
            $this->currentNode()->addChild(new WsNode($token->text));
        } else {
            $this->currentNode()->addChild(new MiscNode($token->text));
        }
    }
}

