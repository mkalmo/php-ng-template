<?php

namespace {

    function render_template($templatePath, $data) {
        $node = new DOMDocument();
        $node->loadHTMLFile($templatePath);

        tpl\traverse($node, $data);

        return $node->saveHTML();
    }

}

namespace tpl {

    require_once('../src/helpers.php');

    const ATTRIBUTE_FOREACH = 'tpl-foreach';
    const ATTRIBUTE_IF = 'tpl-if';

    function traverse($node, $scope) {

        processIf($node, $scope);
        processFor($node, $scope);
        processBind($node, $scope);

        if (hasAttribute($node, ATTRIBUTE_FOREACH)) {
            return; // contents is already processed
        }

        if ($node->childNodes) {
            for ($i = 0; $i < $node->childNodes->length; $i++) {
                $childNode = $node->childNodes->item($i);

                traverse($childNode, $scope);
            }
        }
    }

    function getChildNodes($node) {
        $childNodes = [];

        if ($node->childNodes) {
            for ($i = 0; $i < $node->childNodes->length; $i++) {
                $childNodes []= $node->childNodes->item($i);
            }
        }

        return $childNodes;
    }

    function processBind($node, $scope) {
        if (! $node instanceof \DOMText) {
            return;
        }

        $node->nodeValue = preg_replace_callback(
            '|{{\s*([$a-z0-9\.]*)\s*}}|im',
            function ($m) use ($scope) {
                return $scope[$m[1]];
            },
            $node->wholeText);
    }

    function processIf($node, $scope) {
        $attributes = getAttributes($node);

        $ifs = array_values(array_filter($attributes, function($each) {
            return $each->key == ATTRIBUTE_IF;
        }));

        if (empty($ifs)) {
            return;
        };

        $node->removeAttribute(ATTRIBUTE_IF);

        if (!$scope[$ifs[0]->value]) {
            $parent = $node->parentNode;
            $parent->removeChild($node);
        }
    }

    function processFor($node, $scope) {

        $attributes = getAttributes($node);

        $fors = array_filter($attributes, function($each) {
            return $each->key == 'tpl-foreach';
        });

        if (empty($fors)) {
            return;
        };

        $expression = $fors[0]->value;

        $list = $scope[$expression];

        $parent = $node->parentNode;

        foreach ($list as $each) {
            $newNode = $node->cloneNode(true);
            $newNode->removeAttribute('tpl-foreach');
            $scope['$each'] = $each;
            traverse($newNode, $scope);
            unset($scope['$each']);
            $parent->insertBefore($newNode, $node);
        }

        $parent->removeChild($node);
    }

    function getAttributes($node) {

        if (! $node instanceof \DOMElement) {
            return [];
        }

        $attributes = [];

        for ($j = 0; $j < $node->attributes->length; $j++) {
            $domAttr = $node->attributes->item($j);
            $attributes []= new Entry($domAttr->name, $domAttr->value);
        }

        return $attributes;
    }

    function hasAttribute($node, $attribute) {
        return !!array_find(getAttributes($node), function ($elem) use ($attribute) {
            return $elem->key == $attribute;
        });
    }

    function getAttributeValue($node, $attribute) {
        $found = array_find(getAttributes($node), function ($elem) use ($attribute) {
            return $elem->key == $attribute;
        });

        return $found->value;
    }

    class Entry {
        public $key;
        public $value;

        public function __construct($name, $key) {
            $this->key = $name;
            $this->value = $key;
        }

        public function __toString() {
            return $this->key . "->" . $this->value;
        }
    }
}
