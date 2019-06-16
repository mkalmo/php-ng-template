<?php

namespace tplLib;

require_once 'TagNode.php';

class IncludeNode extends TagNode {

    public function render($scope) {
        $path = $scope->replaceCurlyExpression($this->getExpression());

        $path = $this->removeQuotes($path);

        $html = file_get_contents($scope->mainTemplatePath . '/' . $path);

        $tree = $this->buildTree($html);

        return sprintf('<%1$s%2$s>%3$s</%1$s>',
            $this->name, $this->attributeString($scope), $tree->render($scope));
    }

    private function buildTree($html) {
        $tokens = (new HtmlLexer($html))->tokenize();

        $builder = new TreeBuilderActions();

        (new HtmlParser($tokens, $builder))->parseFragment();

        return $builder->getResult();

    }

    private function removeQuotes($string) {
        $string = preg_replace("/^['\"]/", '', $string);
        $string = preg_replace("/['\"]$/", '', $string);

        return $string;

    }

    private function getExpression() {
        return $this->attributes['tpl-include'];

    }
}
