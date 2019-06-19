<?php

namespace tplLib;

require_once 'RootNode.php';
require_once 'AbstractNode.php';

class TagNode extends AbstractNode {

    protected $name;
    protected $attributes;

    public function __construct($name, $attributes) {
        $this->name = $name;
        $this->attributes = $attributes;
    }

    public function render($scope) {

        $contents = '';
        foreach ($this->children as $child) {
            $contents .= $child->render($scope);
        }

        return sprintf('<%1$s%2$s>%3$s</%1$s>',
            $this->name, $this->attributeString($scope), $contents);
    }

    protected function attributeString($scope) {
        $result = '';
        foreach ($this->attributes as $key => $value) {
            if (strpos($key, 'tpl-') === 0) {
                continue;
            }

            $result .= ' ' . $key;

            if ($value !== null) {
                $result .= '=' . $scope->replaceCurlyExpression($value);
            }
        }

        return $result;
    }

    public function getTagName() {
        return $this->name;
    }

}