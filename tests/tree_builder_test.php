<?php

require_once('ExtendedTextCase.php');
require_once('../src/parser/HtmlParser.php');
require_once('../src/parser/TreeBuilderActions.php');

class TreeBuilderTests extends ExtendedTestCase {

    function buildsTree() {
        $input = '<div>
                    <p>hello</p>
                    <p>hello</p>
                  </div>
                  <!--comment -->
                  <div>
                    <p>hello</p>
                  </div>  ';


        $tokens = (new HtmlLexer($input))->tokenize();

        $builder = new TreeBuilderActions();

        (new HtmlParser($tokens, $builder))->parse();

        $actual = $this->asString($builder->getResult());

        $expected = '[div[W, p[T], W, p[T], W], W, M, W, div[W, p[T], W], W]';

        $this->assertEqual($expected, $actual);
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
