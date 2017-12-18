<?php

function getNodeById($root, $id) {
    if (hasAttribute($root, 'id')
        && getAttributeValue($root, 'id') == $id) {

        return $root;
    }

    foreach (getChildNodes($root) as $childNode) {
        $found = getNodeById($childNode, $id);
        if ($found) {
            return $found;
        }
    }

    return NULL;
}

function getNodeByTagName($node, $name) {
    if (isset($node->nodeName) && $node->nodeName == $name) {
        return $node;
    };

    foreach (getChildNodes($node) as $childNode) {
        $found = getNodeByTagName($childNode, $name);
        if ($found) {
            return $found;
        }
    }

    return NULL;
}

function printStructure($node, $level = 0) {

    $padding = str_repeat ('  ' , $level);

    if (isset($node->nodeName)) {
        print $node->nodeName;
    };

    print $padding . get_class($node) . PHP_EOL;

    foreach (getChildNodes($node) as $each) {
        printStructure($each, $level + 1);
    }

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

function getChildNodes($node) {
    $childNodes = [];

    if ($node->childNodes) {
        for ($i = 0; $i < $node->childNodes->length; $i++) {
            $childNodes []= $node->childNodes->item($i);
        }
    }

    return $childNodes;
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
