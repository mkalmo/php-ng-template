<?php

class Node {
    protected $token;

    private $children = [];

    public function __construct($token) {
        $this->token = $token;
    }

    public function render($scope) {
//        if ($this->token->isRootStartTag()) {
//        }

        $string = $this->token->getContents();

        foreach ($this->children as $child) {
            $string .= $child->render($scope);
        }

        return $string . sprintf('</%s>', $this->token->getTagName());
    }

    public function getTagName() {
        return $this->token->getTagName();
    }

    public function getTokenContents() {
        return $this->token->getContents();
    }

    public function getChildren() {
        return $this->children;
    }

    public function addChild($child) {
        $this->children[] = $child;
    }

    public function addChildren($children) {
        $this->children = array_merge($this->children, $children);
    }

    public function removeChild($child) {
        $predicate = function ($each) use ($child) {
            return $each !== $child;
        };

        $this->children = array_values(array_filter($this->children, $predicate));
    }

    public function insertBefore($new_node, $old_node) {
        $tmp = [];
        foreach ($this->children as $current) {
            if ($current === $old_node) {
                $tmp[] = $new_node;
            }

            $tmp[] = $current;
        }

        $this->children = $tmp;
    }
}
