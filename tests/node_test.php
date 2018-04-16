<?php

require_once('ExtendedTextCase.php');
require_once('../src/parser/Node.php');

class NodeTests extends ExtendedTestCase {

    function removesChildNode() {
        $parent = new Node('parent');

        $c1 = new Node('c1');
        $c2 = new Node('c2');

        $parent->addChild($c1);
        $parent->addChild($c2);

        $this->assertTrue($parent->getChildren()[0] === $c1);
        $this->assertTrue($parent->getChildren()[1] === $c2);

        // $parent->removeChild($node);
        // $parent->insertBefore($newNode, $node);
        // $parent->removeChild($node);


    }

}

(new NodeTests())->run(new TextReporter());