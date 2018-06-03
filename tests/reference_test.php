<?php

require_once('ExtendedTextCase.php');
require_once('../src/parser/Node.php');

class ReferenceTests extends ExtendedTestCase {

    private $array = [1];

    function returningArrayReturnsCopy() {
        $array = $this->getArray();

        $this->assertEqual(1, count($array));

        array_shift($array);

        $this->assertEqual(1, count($this->getArray()));
    }

    private function getArray() {
        return $this->array;
    }

    function passArrayByReference() {
        $array = [1];

        static::removeFirst($array);

        $this->assertEqual(0, count($array));
    }

    function passObjectByReference() {
        $node = new AbstractNode('b');

        static::modifyObject($node);

        $this->assertEqual(1, count($node->children));
    }

    static function modifyObject($object) {
        $object->addChild(new AbstractNode('c'));
    }

    static function removeFirst(&$array) {
        array_shift($array);
    }
}

(new ReferenceTests())->run(new TextReporter());
