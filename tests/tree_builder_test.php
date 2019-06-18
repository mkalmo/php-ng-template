<?php

require_once('ExtendedTextCase.php');
require_once('../src/parser/HtmlParser.php');
require_once('../src/parser/TreeBuilderActions.php');

use tplLib\HtmlLexer;
use tplLib\HtmlParser;
use tplLib\TreeBuilderActions;
use tplLib\TextNode;
use tplLib\MiscNode;
use tplLib\WsNode;

class TreeBuilderTests extends ExtendedTestCase {

    function textOutsideTags() {
        $input = ' a <br> b ';

        $tree = $this->buildNodeTree($input);

        $actual = $this->asString($tree);

        $expected = '[W, T, br[], W, T]';

        $this->assertEqual($expected, $actual);
    }

    function exampleTree() {
        $input = '<div>
                    <p>hello</p>
                    <p>hello</p>
                  </div>
                  <!--comment -->
                  <div>
                    <p>hello</p>
                  </div>  ';

        $tree = $this->buildNodeTree($input);

        $actual = $this->asString($tree);

        $expected = '[div[W, p[T], W, p[T], W], W, M, W, div[W, p[T], W], W]';

        $this->assertEqual($expected, $actual);
    }

    private function buildNodeTree($html) {
        $tokens = (new HtmlLexer($html))->tokenize();

        $builder = new TreeBuilderActions();

        (new HtmlParser($tokens, $builder))->parse();

        return $builder->getResult();
    }

    private function asString($node) {

        if ($node instanceof TextNode) {
            return 'T';
        } else if ($node instanceof MiscNode) {
            return 'M';
        } else if ($node instanceof WsNode) {
            return 'W';
        }

        $child_strings = [];
        foreach ($node->getChildren() as $child) {
            $child_strings[] = self::asString($child);
        }

        return sprintf('%s[%s]', $node->getTagName(), join(', ', $child_strings));
    }
}

!debug_backtrace() && (new TreeBuilderTests())->run(new TextReporter());
