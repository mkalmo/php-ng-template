<?php

require_once 'node/Node.php';

class NodeBuildingActions {

    private $stack;

    public function __construct() {
        $this->stack = [];
        $this->stack[] = new Node('root');
    }

    public function getResult() {
        list ($first) = $this->stack;
        return $first;
    }

    private function currentNode() {
        return $this->stack[count($this->stack) - 1];
    }

    public function tagStartAction($tagName, $attributes) {
//        var_dump('tag start ' . $tagName);

        $node = new Node($tagName, $attributes);

        $this->currentNode()->addChild($node);

        $this->stack[] = $node;
    }

    public function tagEndAction($tagName) {
//        var_dump('tag end ' . $tagName);

        array_pop($this->stack);
    }

    public function voidTagAction($tagName, $attributes) {

    }

    public function staticElementAction($token) {

    }
}

