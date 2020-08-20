<?php declare(strict_types=1);

namespace tplLib;

use \RuntimeException;

require_once 'RootNode.php';
require_once 'AbstractNode.php';

class TagNode extends AbstractNode {

    protected $attributes;
    protected $isVoidTag;
    protected $hasSlashClose;

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
        return $this->isVoidTag
            ? $this->renderVoidTag($scope)
            : $this->renderBodyTag($scope);
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

        if ($this->hasAttribute('tpl-checked')) {
            if ($scope->evaluate($this->getExpression('tpl-checked'))) {
                $result .= ' checked="checked"';
            }
        }

        if ($this->hasAttribute('tpl-selected')) {
            if ($scope->evaluate($this->getExpression('tpl-selected'))) {
                $result .= ' selected="selected"';
            }
        }

        return $result;
    }

    private function hasAttribute($name) {
        foreach ($this->attributes as $key => $value) {
            if ($key === $name) {
                return true;
            }
        }

        return false;
    }

    private function formatAttribute($name, $value) {
        return $value === null
            ? sprintf(' %s', $name)
            : sprintf(' %s=%s', $name, $value);
    }

    protected function getExpression($attributeName) {
        $value = $this->attributes[$attributeName];

        $value = preg_replace("/^['\"]/", '', $value);
        $value = preg_replace("/['\"]$/", '', $value);

        return $value;
    }




}
