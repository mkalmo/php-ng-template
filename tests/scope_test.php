<?php

require_once('../src/tpl.php');
require_once('common.php');
require_once('node_helpers.php');

class ScopeTests extends ExtendedTestCase {

    function canEvaluateStringExpressions() {
        $customer = new Customer('Jack');
        $jill = new Customer('Jill');
        $customer->friends []= $jill;
        $data = ['$customers' => [$customer]];

        $actual = (new tpl\Scope($data))->evaluate(
            '$customers[0]->getFriends()["0"]->name');
        $this->assertEqual('Jill', $actual);
    }

    function missingOffsetReturnsEmptyString() {
        $actual = (new tpl\Scope(['$list' => []]))->evaluate('$list[0]');

        $this->assertEqual('', $actual);
    }

    function canNegateCondition() {
        $actual = (new tpl\Scope(['$flag' => false]))->evaluate('!$flag');

        $this->assertTrue($actual);
    }

    function hasMultipleLayers() {
        $scope = new tpl\Scope();

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
        $scope = new tpl\Scope();

        $this->expectException();

        $scope->removeLayer();
    }
}
