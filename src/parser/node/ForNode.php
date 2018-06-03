<?php

require_once 'TagNode.php';

class ForNode extends TagNode {

    public function render($scope) {

        $parts = preg_split('/\s+as\s+/', $this->getExpression());
        $expression = trim($parts[0]);
        $variableName = trim($parts[1]);
        $variableName = substr($variableName, 1);

        $list = $scope->evaluate($expression);

        $list = $list === null ? [] : $list;

        $result = '';
        $index = 0;
        foreach ($list as $each) {

            $scope->addLayer([
                'first' => $index === 0,
                'last' => $index === count($list) - 1,
                $variableName => $each
            ]);

            $result .= parent::render($scope);

            $scope->removeLayer();

            $index++;
        }

        return $result;
    }

    private function getExpression() {
        $value = $this->attributes['tpl-foreach'];

        $value = preg_replace("/^['\"]/", '', $value);
        $value = preg_replace("/['\"]$/", '', $value);

        return $value;
    }
}
