<?php

require_once('ExtendedTextCase.php');
require_once('../src/parser/HtmlParser.php');
require_once('../src/parser/TreeBuilderActions.php');

class RenderTests extends ExtendedTestCase {

    function _simple() {
        $input = '<div id="1">text<br><br/></div>';

        $tree = $this->buildTree($input);

        $this->assertEqual($input, $tree->render(new Scope()));
    }

    function withWs() {
        $input = '<div> </div>';

        $tree = $this->buildTree($input);

        $this->assertEqual($input, $tree->render(new Scope()));
    }

    function fullRoundTrip() {
        $input = join('', file('test-data/tpl/fragment.html'));
        $input = join('', file('test-data/samples/abc.com.html'));

        $tree = $this->buildTree($input);

        $result = $tree->render(new Scope());

//        print $result;

        $this->assertEqual($input, $result);
    }

    private function buildTree($html) {
        $tokens = (new HtmlLexer($html))->tokenize();

        $builder = new TreeBuilderActions();

        (new HtmlParser($tokens, $builder))->parse();

        return $builder->getResult();
    }
}

!debug_backtrace() && (new RenderTests())->run(new TextReporter());
