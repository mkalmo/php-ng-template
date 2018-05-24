<?php

require_once('ExtendedTextCase.php');
require_once('../src/parser/Parser.php');

class NewParserTests extends ExtendedTestCase {

    function emptyStringReturnsRootTag() {
        $node = (new Parser())->parse('');

        $this->assertEqual('[]', self::asString($node));
    }

    function singleUnclosedTagReturnsSingleNode() {
        $node = (new Parser())->parse('<body>');

        $this->assertEqual('[body[]]', self::asString($node));
    }

    function singleClosedTagReturnsSingleNode() {
        $node = (new Parser())->parse('<body></body>');

        $this->assertEqual('[body[]]', self::asString($node));
    }

    function multipleSiblingsAtZeroLevel() {
        $node = (new Parser())->parse('<hr><img><p>');

        // print self::toString($node);

        // $this->assertEqual('[hr[], img[], p[]]', self::toString($node));
    }

    function multipleClosedSiblingsAtZeroLevel() {
        $node = (new Parser())->parse('<div></div><p><i></i></p>');

        print self::asTree($node);


    }

    function _fromFile() {
        $html = file_get_contents('../tpl/e2e.html');

        $node = (new Parser())->parse($html);

        print self::asString($node);
    }

    function oneUnclosedTagsAtFirstLevel() {
        $node = (new Parser())->parse('<body><hr><hr></body>');

        $this->assertEqual('[body[hr[], hr[]]]', self::asString($node));
    }

    function nodeTreeToString() {
        $r = new Node('r');
        $c1 = new Node('c1');
        $c2 = new Node('c2');
        $c11= new Node('c11');

        $r->addChild($c1);
        $r->addChild($c2);
        $c1->addChild($c11);

        $this->assertEqual('r[c1[c11[]], c2[]]', self::asString($r));
    }

    static function asTree($node, $level = 0) {
        $content = '';
        foreach ($node->getChildren() as $child) {
            $padding = str_repeat('  ', $level);
            $content .= $padding . self::asTree($child, $level + 1);
        }

        return sprintf("%s\n%s</%s>\n",
            $node->getTokenContents(),
            $content,
            $node->getTagName());
    }

    static function asString($node) {
        if (!$node) {
            return '';
        }

        $child_strings = [];
        foreach ($node->getChildren() as $child) {
            $child_strings[] = self::asString($child);
        }

        return sprintf('%s[%s]', $node->getTagName(), join(', ', $child_strings));
    }

}

(new NewParserTests())->run(new TextReporter());