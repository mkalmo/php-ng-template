<?php

namespace tplLib;

require_once 'TagNode.php';

class IfNode extends TagNode {

    public function render($scope) {
        if (!$scope->evaluate($this->getExpression('tpl-if'))) {
            return '';
        }

        return parent::render($scope);
    }
}
