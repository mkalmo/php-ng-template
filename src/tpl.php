<?php

namespace tpl;

const ATTRIBUTE_IF = 'tpl-if';
const ATTRIBUTE_FOR = 'tpl-foreach';
const ATTRIBUTE_INCLUDE = 'tpl-include';

function traverse($node, $scope) {

    processBindOnAttribute($node, $scope);
    processIf($node, $scope);
    processFor($node, $scope);
    processBind($node, $scope);
    processInclude($node, $scope);

    if (hasAttribute($node, ATTRIBUTE_FOR)) {
        return; // contents is already processed
    }

    foreach (getChildNodes($node) as $childNode) {
        traverse($childNode, $scope);
    }
}

function getChildNodes($node) {
    $childNodes = [];

    if (!$node->childNodes) {
        return [];
    }

    for ($i = 0; $i < $node->childNodes->length; $i++) {
        $childNodes []= $node->childNodes->item($i);
    }

    return $childNodes;
}

function processBindOnAttribute($node, $scope) {
    foreach (getAttributes($node) as $entry) {
        $node->removeAttribute($entry->key);
        $node->setAttribute($entry->key,
            replaceCurlyExpression($entry->value, $scope));
    }
}

function processBind($node, $scope) {
    if (! $node instanceof \DOMText) {
        return;
    }

    $node->nodeValue = replaceCurlyExpression($node->nodeValue, $scope);
}

function replaceCurlyExpression($text, $scope) {
    return preg_replace_callback(
        '|{{([^}]+)}}|im',
        function ($matches) use ($scope) {
            return $scope->evaluate($matches[1]);
        },
        $text);
}

function processIf($node, $scope) {
    if (!hasAttribute($node, ATTRIBUTE_IF)) {
        return;
    }

    $expression = getAttributeValue($node, ATTRIBUTE_IF);

    if (!$scope->evaluate($expression)) {
        $parent = $node->parentNode;
        $parent->removeChild($node);
    }

    $node->removeAttribute(ATTRIBUTE_IF);
}

function processInclude($node, $scope) {
    if (!hasAttribute($node, ATTRIBUTE_INCLUDE)) {
        return;
    }

    $filePath = getAttributeValue($node, ATTRIBUTE_INCLUDE);
    $node->removeAttribute(ATTRIBUTE_INCLUDE);

    $contents = file_get_contents($filePath);

    $fragment = new \DOMDocument;
    $fragment->loadHtml($contents);
    $body = $fragment->getElementsByTagName("body")->item(0);
    $children = getChildNodes($body);

    foreach ($children as $child) {
        $imported = $node->ownerDocument->importNode($child, true);
        $node->appendChild($imported);
    }
}

function processFor($node, $scope) {
    if (!hasAttribute($node, ATTRIBUTE_FOR)) {
        return;
    }

    $asExpression = getAttributeValue($node, ATTRIBUTE_FOR);

    $parts = preg_split('/\s+as\s+/', $asExpression);
    $expression = trim($parts[0]);
    $variableName = trim($parts[1]);
    $variableName = str_replace('$', '', $variableName);

    $list = $scope->evaluate($expression);

    $list = $list === null ? [] : $list;

    $parent = $node->parentNode;

    $index = 0;
    foreach ($list as $each) {
        $newNode = $node->cloneNode(true);
        $newNode->removeAttribute(ATTRIBUTE_FOR);
        $parent->insertBefore($newNode, $node);

        $scope->addLayer([
            'first' => $index === 0,
            'last' => $index === count($list) - 1,
            $variableName => $each
        ]);
        traverse($newNode, $scope);
        $scope->removeLayer();

        $index++;
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
