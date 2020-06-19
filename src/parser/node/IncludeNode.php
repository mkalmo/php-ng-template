<?php

namespace tplLib;

require_once 'TagNode.php';
require_once __DIR__ . '/../helpers.php';

class IncludeNode extends TagNode {

    public function render($scope) {

        $path = $scope->replaceCurlyExpression($this->getExpression('tpl-include'));

        if (strlen($path) === 0) {
            throw new \Exception("tpl-include file path is missing");
        }

        $path = $scope->mainTemplatePath . '/' . $path;

        $html = loadContents($path);

        $tree = $this->buildTree($html);

        $this->addChild($tree);

        return parent::render($scope);
    }

    private function buildTree($html) {
        $tokens = (new HtmlLexer($html))->tokenize();

        $builder = new TreeBuilderActions();

        (new HtmlParser($tokens, $builder))->parseFragment();

        return $builder->getResult();
    }
}
