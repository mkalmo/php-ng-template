<?php

class Node {
    private $tag;
    public $children = [];

    public function __construct($tag) {
        $this->tag = $tag;
    }

    public function getTag() {
        return $this->tag;
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
}
