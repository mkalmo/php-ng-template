<?php

require_once('ExtendedTextCase.php');
require_once('../src/parser/HtmlParser.php');
require_once('../src/parser/TreeBuilderActions.php');
require_once('../src/parser/DebugActions.php');

class RenderTests extends ExtendedTestCase {

    function simple() {
        $input = '<div id="1">text<br><br/></div>';

        $tree = $this->buildTree($input);

        $this->assertEqual($input, $tree->render(new Scope()));
    }

    function withWs() {
        $input = '<div> </div>';

        $tree = $this->buildTree($input);

        $this->assertEqual($input, $tree->render(new Scope()));
    }

    private function buildTree($html) {
        $tokens = (new HtmlLexer($html))->tokenize();

        $builder = new TreeBuilderActions();

        (new HtmlParser($tokens, $builder))->parse();

        return $builder->getResult();
    }
}

!debug_backtrace() && (new RenderTests())->run(new TextReporter());
