<?php

namespace {

    use tpl\Scope;

    function render_template($templatePath, $data = []) {
        $node = new DOMDocument();
        $node->loadHTMLFile($templatePath);

        tpl\traverse($node, new Scope($data));

        return $node->saveHTML();
    }

}

namespace tpl {

    const ATTRIBUTE_IF = 'tpl-if';
    const ATTRIBUTE_FOR = 'tpl-foreach';
    const ATTRIBUTE_INCLUDE = 'tpl-include';

    function traverse($node, $scope) {

        processBindOnAttribute($node, $scope);
        processIf($node, $scope);
        processFor($node, $scope);
        processBind($node, $scope);
        processInclude($node, $scope);

        if (hasAttribute($node, ATTRIBUTE_FOR)) {
            return; // contents is already processed
        }

        foreach (getChildNodes($node) as $childNode) {
            traverse($childNode, $scope);
        }
    }

    function getChildNodes($node) {
        $childNodes = [];

        if (!$node->childNodes) {
            return [];
        }

        for ($i = 0; $i < $node->childNodes->length; $i++) {
            $childNodes []= $node->childNodes->item($i);
        }

        return $childNodes;
    }

    function processBindOnAttribute($node, $scope) {
        foreach (getAttributes($node) as $entry) {
            $node->removeAttribute($entry->key);
            $node->setAttribute($entry->key,
                replaceCurlyExpression($entry->value, $scope));
        }
    }

    function processBind($node, $scope) {
        if (! $node instanceof \DOMText) {
            return;
        }

        $node->nodeValue = replaceCurlyExpression($node->wholeText, $scope);
    }

    function replaceCurlyExpression($text, $scope) {
        return preg_replace_callback(
            '|{{\s*(\$[^\s]+)\s*}}|im',
            function ($matches) use ($scope) {
                return $scope->evaluate($matches[1]);
            },
            $text);
    }

    function processIf($node, $scope) {
        if (!hasAttribute($node, ATTRIBUTE_IF)) {
            return;
        }

        $expression = getAttributeValue($node, ATTRIBUTE_IF);

        if (!$scope->evaluate($expression)) {
            $parent = $node->parentNode;
            $parent->removeChild($node);
        }

        $node->removeAttribute(ATTRIBUTE_IF);
    }

    function processInclude($node, $scope) {
        if (!hasAttribute($node, ATTRIBUTE_INCLUDE)) {
            return;
        }

        $filePath = getAttributeValue($node, ATTRIBUTE_INCLUDE);
        $node->removeAttribute(ATTRIBUTE_INCLUDE);

        $contents = file_get_contents($filePath);

        $newNode = $node->ownerDocument->createDocumentFragment();
        $newNode->appendXML($contents);
        $node->appendChild($newNode);
    }

    function processFor($node, $scope) {
        if (!hasAttribute($node, ATTRIBUTE_FOR)) {
            return;
        }

        $asExpression = getAttributeValue($node, ATTRIBUTE_FOR);

        $parts = preg_split('/\s+as\s+/', $asExpression);
        $expression = trim($parts[0]);
        $variableName = trim($parts[1]);

        $list = $scope->evaluate($expression);

        $parent = $node->parentNode;

        $first = true;
        foreach ($list as $each) {
            $newNode = $node->cloneNode(true);
            $newNode->removeAttribute(ATTRIBUTE_FOR);
            $parent->insertBefore($newNode, $node);

            $scope->addLayer(['$first' => $first, $variableName => $each]);
            traverse($newNode, $scope);
            $scope->removeLayer();

            $first = false;
        }

        $parent->removeChild($node);
    }

    function getAttributes($node) {

        if (! $node instanceof \DOMElement) {
            return [];
        }

        $attributes = [];

        for ($j = 0; $j < $node->attributes->length; $j++) {
            $domAttr = $node->attributes->item($j);
            $attributes []= new Entry($domAttr->name, $domAttr->value);
        }

        return $attributes;
    }

    function hasAttribute($node, $attribute) {
        return !!array_find(getAttributes($node), function ($elem) use ($attribute) {
            return $elem->key == $attribute;
        });
    }

    function getAttributeValue($node, $attribute) {
        $found = array_find(getAttributes($node), function ($elem) use ($attribute) {
            return $elem->key == $attribute;
        });

        return $found->value;
    }

    class Scope {
        private $data;
        private $layers = [];

        public function __construct($data = []) {
            $this->addLayer($data);
        }

        public function evaluate($expression) {
            $parts = preg_split('/(?=\[)|->/' , $expression);
            $rootString = array_shift($parts);

            $negated = false;
            if (preg_match('/^!\s*/' , $rootString)) {
                $rootString = preg_replace('/^!\s*/', '', $rootString);
                $negated = true;
            }

            $result = $this->getEntry($rootString);

            if ($result === null) {
                return '';
            }

            foreach ($parts as $part) {
                if (preg_match('/^\w+$/' , $part)) {
                    $result = $result->$part;
                }
                if (preg_match('/^(\w+)\(\)$/', $part, $matches)) {
                    $methodName = $matches[1];
                    $result = $result->$methodName();
                }
                if (preg_match('/^\[([^\]]+)\]$/' , $part, $matches)) {
                    $index = preg_replace('/["\']/', '', $matches[1]);
                    $result = $result[$index];
                }
            }

            return $negated ? !$result : $result;
        }

        public function addLayer($data = []) {
            $this->layers []= $data;
            $this->data = & $this->layers[sizeof($this->layers) - 1];
        }

        public function removeLayer() {
            if (count($this->layers) == 1) {
                throw new \Exception("can't remove last layer");
            }

            array_pop($this->layers);

            $this->data = & $this->layers[sizeof($this->layers) - 1];
        }

        public function addEntry($key, $value) {
            $this->data[$key] = $value;
        }

        public function getEntry($key) {
            foreach (array_reverse($this->layers) as $layer) {
                if (isset($layer[$key])) {
                    return $layer[$key];
                }
            }

            return null;
        }

        public function removeEntry($key) {
            unset($this->data[$key]);
        }

        public function __toString() {
            return '' . print_r($this->layers, true);
        }
    }

    class Entry {
        public $key;
        public $value;

        public function __construct($name, $key) {
            $this->key = $name;
            $this->value = $key;
        }

        public function __toString() {
            return $this->key . "->" . $this->value;
        }
    }

    function array_find($array, $predicate) {
        $list = array_values(array_filter($array, $predicate));
        if (sizeof($list) > 1) {
            throw new UnexpectedValueException("found more than one");
        }

        return sizeof($list) == 0 ? NULL : $list[0];
    }
}

