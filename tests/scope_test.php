<?php

require_once('../src/tpl.php');
require_once '../src/Scope.php';
require_once('common.php');
require_once('node_helpers.php');
require_once('Customer.class.php');

class ScopeTests extends ExtendedTestCase {

    function canEvaluateStringExpressions() {
        $customer = new Customer('Jack');
        $jill = new Customer('Jill');
        $customer->friends []= $jill;
        $data = ['customers' => [$customer]];

        $actual = (new tpl\Scope($data))->evaluate(
            '$customers[0]->getFriends()["0"]->name');
        $this->assertEqual('Jill', $actual);
    }

    function canEvaluatePhpFunctions() {
        $data = ['items' => [1, 2, 3]];

        $actual = (new tpl\Scope($data))->evaluate(
            'join(", ", $items)');
        $this->assertEqual('1, 2, 3', $actual);

    }

    function canNegateCondition() {
        $actual = (new tpl\Scope(['flag' => false]))->evaluate('!$flag');

        $this->assertTrue($actual);
    }

    function hasMultipleLayers() {
        $scope = new tpl\Scope(['key' => 1]);
        $this->assertEqual(1, $scope->getEntry('key'));

        $scope->addLayer([]);
        $this->assertEqual(1, $scope->getEntry('key')); // visible from previous layer

        $scope->addLayer(['key' => 2]);
        $this->assertEqual(2, $scope->getEntry('key')); // hidden by 2

        $scope->removeLayer();
        $this->assertEqual(1, $scope->getEntry('key')); // visible again
    }

    function removingLastScopeLayerThrows() {
        $scope = new tpl\Scope();

        $this->expectException();

        $scope->removeLayer();
    }
}

(new ScopeTests())->run(new TextReporter());
