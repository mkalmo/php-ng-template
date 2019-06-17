<?php

namespace tplLib;

require_once 'TagNode.php';
require_once dirname(__FILE__) . '/../helpers.php';

class IncludeNode extends TagNode {

    public function render($scope) {

        $path = $scope->replaceCurlyExpression($this->getExpression());

        $path = $this->removeQuotes($path);

        if (strlen($path) === 0) {
            throw new \Exception("tpl-include file path is missing");
        }

        $path = $scope->mainTemplatePath . '/' . $path;

        $html = read_file($path);

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
