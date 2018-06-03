<?php

abstract class AbstractNode {

    protected $token;

    protected $children = [];

    public function __construct($token) {
        $this->token = $token;
    }

    public abstract function render($scope);

    public function getTagName() {
        return $this->token;


//        return $this->token->getTagName();
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
