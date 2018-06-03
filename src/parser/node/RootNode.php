<?php

require_once 'AbstractNode.php';

class RootNode extends AbstractNode {

    public function __construct() {
        parent::__construct(null);
    }

    public function render($scope) {
        $string = '';
        foreach ($this->children as $child) {
            $string .= $child->render($scope);
        }
        return $string;
    }

}
