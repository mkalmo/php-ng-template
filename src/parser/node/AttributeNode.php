<?php

namespace tplLib;

require_once 'RootNode.php';
require_once 'AbstractNode.php';

class AttributeNode extends TagNode {

    private $tplAttributeName;
    private $attributeName;

    public function __construct($name, $attributes,
                                $tplAttributeName, $attributeName) {
        parent::__construct($name, $attributes);

        $this->tplAttributeName = $tplAttributeName;
        $this->attributeName = $attributeName;
    }

    public function render($scope) {
        if ($scope->evaluate($this->getExpression($this->tplAttributeName))) {
            $this->attributes[$this->attributeName] =
                sprintf('"%s"', $this->attributeName);
        }

        return parent::render($scope);
    }
}
