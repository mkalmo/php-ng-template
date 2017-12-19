<?php

require_once('../src/helpers.php');
require_once('./node_helpers.php');
require_once('../src/tiny-template.php');

class ParserTests extends UnitTestCase {

    function test_canCheckAttributeExistence() {
        $node = $this->createNode('<div id="1"></div>');

        $this->assertTrue(tpl\hasAttribute($node, 'id'));
    }

    function test_canGetAttributeValue() {
        $node = $this->createNode('<div id="1"></div>');
        $this->assertEqual('1',
            tpl\getAttributeValue($node, 'id'));
    }

    function test_canProcessBindExpression() {
        $doc = new DOMDocument('1.0');
        $node = $doc->createTextNode('{{ $var }}');
        $doc->appendChild($node);

        tpl\processBind($node, new tpl\Scope(['$var' => 'hello']));

        $this->assertEqual('hello' . PHP_EOL, $this->asText($doc));
    }

    function test_canProcessBindExpressionsInAttributes() {
        $node = $this->createNode('<input value="{{ $var }}" />');

        tpl\processBindOnAttribute($node, new tpl\Scope(['$var' => 'hello']));

        $this->assertEqual('<input value="hello">', $this->asText($node));
    }

    function test_whenIfConditionIsTrue_contentRemains() {
        $node = $this->createNode('<div tpl-if="$flag">1</div>');

        tpl\processIf($node, new tpl\Scope(['$flag' => true]));

        $this->assertEqual('<div>1</div>', $this->asText($node));
    }

    function test_whenIfConditionIsFalse_contentIsRemoved() {
        $node = $this->createNode('<div tpl-if="$flag">42</div>');

        $parent = $node->parentNode;

        tpl\processIf($node, new tpl\Scope(['$flag' => false]));

        $this->assertNoPattern('/42/', $this->asText($parent));
    }

    function test_canProcessForExpression() {
        $node = $this->createNode('<div tpl-foreach="$list">{{ $each }}</div>');

        $parent = $node->parentNode;

        tpl\processFor($node, new tpl\Scope(['$list' => [42, 24]]));

        $this->assertPattern('/42/', $this->asText($parent));
        $this->assertPattern('/24/', $this->asText($parent));
    }

    function createNode($source) {
        $doc = new DOMDocument('1.0');
        $doc->loadHTML($source);
        return getNodeByTagName($doc, 'body')->firstChild;
    }

    function getDocument($node) {
        while (! $node instanceof \DOMDocument) {
            if (!$node) {
                throw new RuntimeException("does not have parent of type DOMDocument");
            }

            $node = $node->parentNode;
        }

        return $node;

    }

    function asText($node) {
        return $this->getDocument($node)->saveHTML($node);
    }
}

class ArrayFindTests extends UnitTestCase {

    function test_elementExists_returnsIt() {
        $found = array_find([1, 2, 3], function ($element) {
            return $element == 2;
        });

        $this->assertEqual(2, $found);
    }

    function test_elementDoesNotExist_returnNULL() {
        $found = array_find([1, 2, 3], function ($element) {
            return $element == 4;
        });

        $this->assertNull($found);
    }

    function test_findsMoreThanOne_throw() {
        $this->expectException(UnexpectedValueException::class);

        array_find([1, 2, 3, 2], function ($element) {
            return $element == 2;
        });
    }
}
