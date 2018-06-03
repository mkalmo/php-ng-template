<?php

require_once('ExtendedTextCase.php');
require_once('../src/Scope.php');
require_once('Customer.class.php');

class ScopeTests extends ExtendedTestCase {

    function canEvaluateStringExpressions() {
        $customer = new Customer('Jack');
        $jill = new Customer('Jill');
        $customer->friends []= $jill;
        $data = ['$customers' => [$customer]];

        $actual = (new Scope($data))->evaluate(
            '$customers[0]->getFriends()["0"]->name');
        $this->assertEqual('Jill', $actual);
    }

    function missingOffsetReturnsEmptyString() {
        $actual = (new Scope(['$list' => []]))->evaluate('$list[0]');

        $this->assertEqual('', $actual);
    }

    function canNegateCondition() {
        $actual = (new Scope(['$flag' => false]))->evaluate('!$flag');

        $this->assertTrue($actual);
    }

    function hasMultipleLayers() {
        $scope = new Scope();

        $scope->addEntry('key', 1);
        $this->assertEqual(1, $scope->getEntry('key'));
        $scope->addLayer();
        $this->assertEqual(1, $scope->getEntry('key'));
        $scope->addEntry('key', 2);
        $this->assertEqual(2, $scope->getEntry('key'));
        $scope->removeLayer();
        $this->assertEqual(1, $scope->getEntry('key'));
    }

    function removingLastScopeLayerThrows() {
        $scope = new Scope();

        $this->expectException();

        $scope->removeLayer();
    }
}

//(new ScopeTests())->run(new TextReporter());