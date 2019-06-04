<?php

namespace tplLib;

require_once 'RootNode.php';
require_once 'AbstractNode.php';

class VoidTagNode extends TagNode {

    private $hasSlashClose;

    public function __construct($name, $attributes, $hasSlashClose) {
        $this->name = $name;
        $this->attributes = $attributes;
        $this->hasSlashClose = $hasSlashClose;
    }

    public function render($scope) {
        $contents = '';
        foreach ($this->children as $child) {
            $contents .= $child->render($scope);
        }

        $close = $this->hasSlashClose ? '/>' : '>';

        return '<' . $this->name . $this->attributeString($scope) . $close;
    }

    public function getTagName() {
        return $this->name;
    }

}
