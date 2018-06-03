<?php

require_once('../src/helpers.php');
require_once('./node_helpers.php');
require_once('../src/tpl.php');
require_once('../src/Scope.php');
require_once('../src/Entry.php');
require_once('ExtendedTextCase.php');
require_once('Customer.class.php');

require_once('../src/parser/HtmlParser.php');
require_once('../src/parser/HtmlLexer.php');
require_once('../src/parser/TreeBuilderActions.php');


class TemplateTests extends ExtendedTestCase {

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

    function whenIfConditionIsTrue_tagRemains() {
        $tree = $this->buildTree('<div tpl-if="$flag">1</div>');

        $scope = new Scope(['$flag' => true]);

        $this->assertEqual('<div>1</div>', $tree->render($scope));
    }

    function whenIfConditionIsFalse_tagIsRemoved() {
        $tree = $this->buildTree('<div tpl-if="$flag">1</div>');

        $scope = new Scope(['$flag' => false]);

        $this->assertEqual('', $tree->render($scope));
    }

    function _canProcessForExpression() {
        $tree = $this->buildTree('<p tpl-foreach="$list as $each">{{ $each }}</p>');

        $scope = new Scope(['$list' => [1, 2]]);

        print $tree->render($scope);

//        $this->assertEqual('', $tree->render($scope));

//        $node = $this->createNode('<div tpl-foreach="$list as $each">{{ $each }}</div>');
//
//        $parent = $node->parentNode;
//
//        tpl\processFor($node, new tpl\Scope(['$list' => [42, 24]]));
//
//        $this->assertPattern('/42/', $this->asText($parent));
//        $this->assertPattern('/24/', $this->asText($parent));
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

    private function buildTree($html) {
        $tokens = (new HtmlLexer($html))->tokenize();

        $builder = new TreeBuilderActions();

        (new HtmlParser($tokens, $builder))->parse();

        return $builder->getResult();

    }

}

!debug_backtrace() && (new TemplateTests())->run(new TextReporter());