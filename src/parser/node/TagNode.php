<?php

namespace tplLib;

use \RuntimeException;

require_once 'RootNode.php';
require_once 'AbstractNode.php';

class TagNode extends AbstractNode {

    protected $attributes;
    protected $isVoidTag;
    protected $hasSlashClose;
    private $tplAttributes = [];
    private $tmpAttributes = [];

    public function __construct($name, $attributes) {
        parent::__construct($name);
        $this->attributes = $attributes;
    }

    public function makeVoid() {
        $this->isVoidTag = true;
    }

    public function addSlashClose() {
        if (!$this->isVoidTag) {
            throw new RuntimeException('must be void tag');
        }

        $this->hasSlashClose = true;
    }

    public function render($scope) {
        $this->processTplAttributes($scope);

        if ($this->isVoidTag) {
            $result = $this->renderVoidTag($scope);
        } else {
            $result = $this->renderBodyTag($scope);
        }

        $this->tmpAttributes = [];

        return $result;
    }

    public function processTplAttributes($scope) {
        foreach ($this->tplAttributes as $each) {
            $tplName = $each[0];
            $htmlName = $each[1];

            if ($scope->evaluate($this->getExpression($tplName))) {
                $this->tmpAttributes[$htmlName] = sprintf('"%s"', $htmlName);
            }
        }
    }

    public function renderVoidTag($scope) {
        $close = $this->hasSlashClose ? '/' : '';

        return sprintf('<%s%s%s>',
            $this->name, $this->attributeString($scope), $close);
    }

    public function renderBodyTag($scope) {

        $contents = '';
        foreach ($this->children as $child) {
            $contents .= $child->render($scope);
        }

        if ($this->name === 'tpl') {
            return $contents;
        }

        return sprintf('<%1$s%2$s>%3$s</%1$s>',
            $this->name, $this->attributeString($scope), $contents);
    }

    protected function attributeString($scope) {
        $result = '';
        foreach ($this->attributes as $key => $value) {
            if (strpos($key, 'tpl-') === 0) {
                continue;
            }

            $result .= $this->formatAttribute($key,
                $scope->replaceCurlyExpression($value));
        }

        // tpl-selected, tpl-checked
        foreach ($this->tplAttributes as $each) {
            $tplName = $each[0];
            $htmlName = $each[1];

            if ($scope->evaluate($this->getExpression($tplName))) {
                $result .= $this->formatAttribute($htmlName,
                    sprintf('"%s"', $htmlName));
            }
        }

        return $result;
    }

    private function formatAttribute($name, $value) {
        return $value === null
            ? sprintf(' %s', $name)
            : sprintf(' %s=%s', $name, $value);
    }

    public function addTplAttribute($tplAttributeName, $attributeName) {
        $this->tplAttributes[] = [$tplAttributeName, $attributeName];
    }

    protected function getExpression($attributeName) {
        $value = $this->attributes[$attributeName];

        $value = preg_replace("/^['\"]/", '', $value);
        $value = preg_replace("/['\"]$/", '', $value);

        return $value;
    }




}
