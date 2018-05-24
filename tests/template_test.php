<?php

require_once('../src/helpers.php');
require_once('./node_helpers.php');
require_once('../src/tpl.php');
require_once('../src/Scope.php');
require_once('../src/Entry.php');
require_once('ExtendedTextCase.php');
require_once('Customer.class.php');


class ParserTests extends ExtendedTestCase {

    function canCheckAttributeExistence() {
        $node = $this->createNode('<div id="1"></div>');

        $this->assertTrue(tpl\hasAttribute($node, 'id'));
    }

    function canGetAttributeValue() {
        $node = $this->createNode('<div id="1"></div>');
        $this->assertEqual('1',
            tpl\getAttributeValue($node, 'id'));
    }

    function canProcessBindExpression() {
        $doc = new DOMDocument('1.0');
        $node = $doc->createTextNode('{{ $var1 }}{{ $var2 }}');
        $doc->appendChild($node);

        tpl\processBind($node, new tpl\Scope(['$var1' => 'hello']));

        $this->assertEqual('hello' . PHP_EOL, $this->asText($doc));
    }

    function canProcessBindComplexExpression() {
        $doc = new DOMDocument('1.0');
        $node = $doc->createTextNode('{{ $c->name }}');
        $doc->appendChild($node);

        tpl\processBind($node, new tpl\Scope(['$c' => new Customer('Jack')]));

        $this->assertEqual('Jack' . PHP_EOL, $this->asText($doc));
    }

    function canProcessBindExpressionsInAttributes() {
        $node = $this->createNode('<input value="{{ $var }}" />');

        tpl\processBindOnAttribute($node, new tpl\Scope(['$var' => 'hello']));

        $this->assertEqual('<input value="hello">', $this->asText($node));
    }

    function whenIfConditionIsTrue_contentRemains() {
        $node = $this->createNode('<div tpl-if="$flag">1</div>');

        tpl\processIf($node, new tpl\Scope(['$flag' => true]));

        $this->assertEqual('<div>1</div>', $this->asText($node));
    }

    function whenIfConditionIsFalse_contentIsRemoved() {
        $node = $this->createNode('<div tpl-if="$flag">42</div>');

        $parent = $node->parentNode;

        tpl\processIf($node, new tpl\Scope(['$flag' => false]));

        $this->assertNoPattern('/42/', $this->asText($parent));
    }

    function canProcessForExpression() {
        $node = $this->createNode('<div tpl-foreach="$list as $each">{{ $each }}</div>');

        $parent = $node->parentNode;

        tpl\processFor($node, new tpl\Scope(['$list' => [42, 24]]));

        $this->assertPattern('/42/', $this->asText($parent));
        $this->assertPattern('/24/', $this->asText($parent));
    }

    function forHasFirstAndLastVariables() {
        $node = $this->createNode(
            '<b tpl-foreach="$list as $each">{{ $first }}{{ $last }}</b>');

        $parent = $node->parentNode;

        tpl\traverse($node, new tpl\Scope(['$list' => [1, 2, 3]]));

        $this->assertEqual(
            "<body>\n<b>1</b><b></b><b>1</b>\n</body>",
            $this->asText($parent));
    }

    function canProcessNestedForLoops() {
        $node = $this->createNode(
            '<b tpl-foreach="$list1 as $each">' .
            '  {{ $each }}' .
            '  <b tpl-foreach="$list2 as $each">{{ $each }}</b>' .
            '  {{ $each }}' .
            '</b>');

        $parent = $node->parentNode;

        tpl\traverse($node, new tpl\Scope(['$list1' => [1], '$list2' => [2]]));

        $this->assertEqual(
            '<body><b>  1  <b>2</b>  1</b></body>',
            $this->asText($parent));
    }

    private function createNode($source) {
        $doc = new DOMDocument('1.0');
        $doc->loadHTML($source);
        return $doc->getElementsByTagName('body')->item(0)->firstChild;
    }

    private function asText($node) {
        $doc = $node instanceof \DOMDocument ? $node : $node->ownerDocument;
        return $doc->saveHTML($node);
    }
}

(new ParserTests())->run(new TextReporter());