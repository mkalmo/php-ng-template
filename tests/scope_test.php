<?php

require_once('ExtendedTextCase.php');

require_once '../src/Scope.php';
require_once('Customer.class.php');

use tplLib\Scope;

class ScopeTests extends ExtendedTestCase {

    function canEvaluateStringExpressions() {
        $customer = new Customer('Jack');
        $jill = new Customer('Jill');
        $customer->friends []= $jill;
        $data = ['customers' => [$customer]];

        $actual = (new Scope($data))->evaluate(
            '$customers[0]->getFriends()["0"]->name');
        $this->assertEqual('Jill', $actual);
    }

    function canEvaluatePhpFunctions() {
        $data = ['items' => [1, 2, 3]];

        $actual = (new Scope($data))->evaluate(
            'join(", ", $items)');
        $this->assertEqual('1, 2, 3', $actual);

    }

    function missingValueIsBlank() {
        $actual = (new Scope())->evaluate('$missing');

        $this->assertEqual('', $actual);
    }

    function canNegateCondition() {
        $actual = (new Scope(['flag' => false]))->evaluate('!$flag');

        $this->assertTrue($actual);
    }

    function canNegateMissingValue() {
        $actual = (new Scope())->evaluate('!$missing');

        $this->assertTrue($actual);
    }

    function hasMultipleLayers() {
        $scope = new Scope(['key' => 1]);
        $this->assertEqual(1, $scope->getEntry('key'));

        $scope->addLayer([]);
        $this->assertEqual(1, $scope->getEntry('key')); // visible from previous layer

        $scope->addLayer(['key' => 2]);
        $this->assertEqual(2, $scope->getEntry('key')); // hidden by 2

        $scope->removeLayer();
        $this->assertEqual(1, $scope->getEntry('key')); // visible again
    }

    function removingLastScopeLayerThrows() {
        $scope = new Scope();

        $this->expectException(new Exception("can't remove last layer"));

        $scope->removeLayer();
    }

    function _replaceCurlyExpression() {
        $actual = (new Scope())->replaceCurlyExpression(
            '{{ 1 }} {{ 1 + 1 }} {{ "&<" }}');

        $this->assertEqual("1 2 &amp;&lt;", $actual);
    }

    function replaceCurlyExpressionEvalError() {
        $this->expectException(new Exception("error evaluating  a "));

        (new Scope())->replaceCurlyExpression('{{ a }}');
    }
}

!debug_backtrace() && (new ScopeTests())->run(new TextReporter());
