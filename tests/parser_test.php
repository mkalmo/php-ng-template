<?php

require_once('ExtendedTextCase.php');
require_once('../src/parser/Parser.php');

class NewParserTests extends ExtendedTestCase {

    function emptyStringReturnsRootTag() {
        $node = (new Parser())->parse('');

        $this->assertEqual('[]', self::toString($node));
    }

    function singleUnclosedTagReturnsSingleNode() {
        $node = (new Parser())->parse('<body>');

        $this->assertEqual('[body[]]', self::toString($node));
    }

    function singleClosedTagReturnsSingleNode() {
        $node = (new Parser())->parse('<body></body>');

        $this->assertEqual('[body[]]', self::toString($node));
    }

    function _multipleSiblingsAtZeroLevel() {
        $node = (new Parser())->parse('<hr><img><p>');

        print self::toString($node);

//        print_r($node);

//        $this->assertEqual('[hr[], img[], p[]]', self::toString($node));
    }

    function oneUnclosedTagsAtFirstLevel() {
        $node = (new Parser())->parse('<body><hr><hr></body>');

        $this->assertEqual('[body[hr[], hr[]]]', self::toString($node));
    }

    function nodeTreeToString() {
        $r = new Node('r');
        $c1 = new Node('c1');
        $c2 = new Node('c2');
        $c11= new Node('c11');

        $r->addChild($c1);
        $r->addChild($c2);
        $c1->addChild($c11);

        $this->assertEqual('r[c1[c11[]], c2[]]', self::toString($r));
    }

    static function toString($node) {
        if (!$node) {
            return '';
        }

        $child_strings = [];
        foreach ($node->children as $child) {
            $child_strings[] = self::toString($child);
        }

        return sprintf('%s[%s]', $node->tag, join(', ', $child_strings));
    }

}

(new NewParserTests())->run(new TextReporter());