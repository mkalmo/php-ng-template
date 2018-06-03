<?php

require_once('ExtendedTextCase.php');
require_once('Customer.class.php');

require_once('../src/Scope.php');
require_once('../src/parser/HtmlParser.php');
require_once('../src/parser/HtmlLexer.php');
require_once('../src/parser/TreeBuilderActions.php');


class TemplateTests extends ExtendedTestCase {

    function canProcessBindExpression() {
        $tree = $this->buildTree('{{ $var1 }}{{ $var2 }}{{ $var1 }}');

        $scope = new Scope(['var1' => '1']);

        $this->assertEqual('11', $tree->render($scope));
    }

    function canProcessBindComplexExpression() {
        $tree = $this->buildTree('{{ $c->name }}');

        $scope = new Scope(['c' => new Customer('Jack')]);

        $this->assertEqual('Jack', $tree->render($scope));
    }

    function canProcessBindExpressionsInAttributes() {
        $tree = $this->buildTree('<input value="{{ $var }}" />');

        $scope = new Scope(['var' => 'ok']);

        $this->assertEqual('<input value="ok"/>', $tree->render($scope));
    }

    function whenIfConditionIsTrue_tagRemains() {
        $tree = $this->buildTree('<div tpl-if="$flag">1</div>');

        $scope = new Scope(['flag' => true]);

        $this->assertEqual('<div>1</div>', $tree->render($scope));
    }

    function whenIfConditionIsFalse_tagIsRemoved() {
        $tree = $this->buildTree('<div tpl-if="$flag">1</div>');

        $scope = new Scope(['flag' => false]);

        $this->assertEqual('', $tree->render($scope));
    }

    function canProcessForExpression() {
        $tree = $this->buildTree('<p tpl-foreach="$list as $each">{{ $each }}</p>');

        $scope = new Scope(['list' => [1, 2]]);

        $this->assertEqual('<p>1</p><p>2</p>', $tree->render($scope));
    }

    function forHasFirstAndLastVariables() {
        $tree = $this->buildTree('<p tpl-foreach="$list as $each">{{ $first }}{{ $last }}</p>');

        $scope = new Scope(['list' => [1, 2, 3]]);

        $this->assertEqual('<p>1</p><p></p><p>1</p>', $tree->render($scope));
    }

    function canProcessNestedForLoops() {
        $input = '<p tpl-foreach="$list1 as $each">' .
                 '{{ $each }}' .
                 '<i tpl-foreach="$list2 as $each">{{ $each }}</i>' .
                 '{{ $each }}' .
                 '</p>';


        $tree = $this->buildTree($input);

        $scope = new Scope(['list1' => [1], 'list2' => [2, 3]]);

        $this->assertEqual('<p>1<i>2</i><i>3</i>1</p>', $tree->render($scope));
    }

    private function buildTree($html) {
        $tokens = (new HtmlLexer($html))->tokenize();

        $builder = new TreeBuilderActions();

        (new HtmlParser($tokens, $builder))->parse();

        return $builder->getResult();

    }

}

!debug_backtrace() && (new TemplateTests())->run(new TextReporter());