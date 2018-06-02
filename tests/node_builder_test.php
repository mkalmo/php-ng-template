<?php

require_once('ExtendedTextCase.php');
require_once('../src/parser/HtmlParser.php');
require_once('../src/parser/NodeBuildingActions.php');

class NodeBuilderTests extends ExtendedTestCase {

    function test1() {
        $input = '<div><p>hello</p><p>hello</p></div><div><p>hello</p><p>hello</p></div>';

        $tokens = (new HtmlLexer($input))->tokenize();

        $nodeBuildingActions = new NodeBuildingActions();

        (new HtmlParser($tokens, $nodeBuildingActions))->parse();

        print $this->asString($nodeBuildingActions->getResult());

    }

    private function asString($node) {
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

(new NodeBuilderTests())->run(new TextReporter());