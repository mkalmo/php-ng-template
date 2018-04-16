<?php

class Node {
    protected $startTag;

    private $children = [];

    public function __construct($startTag) {
        $this->startTag = $startTag;
    }

    public function render($scope) {
        if ($this->startTag === null) {
            $string = '';
            foreach ($this->children as $child) {
                $string .= $child->render($scope);
            }
            return $string;
        }

        $string = $this->startTag;

        foreach ($this->children as $child) {
            $string .= $child->render($scope);
        }

        //return sprintf('</%s>', $this->getTagName());

        var_dump($this);

        return $string;
    }

    public function getTagName() {
        if ($this->startTag === null) {
            return '';
        }

        preg_match("/<(\w+)/", $this->startTag, $matches);

        list ($whole_match, $expression) = $matches;

        return $expression;
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
