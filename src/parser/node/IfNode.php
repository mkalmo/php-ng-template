<?php

namespace tplLib;

require_once 'TagNode.php';

class IfNode extends TagNode {

    public function render($scope) {
        if (!$scope->evaluate($this->getExpression())) {
            return '';
        }

        return parent::render($scope);
    }

    private function getExpression() {
        $value = $this->attributes['tpl-if'];

        $value = preg_replace("/^['\"]/", '', $value);
        $value = preg_replace("/['\"]$/", '', $value);

        return $value;
    }
}
