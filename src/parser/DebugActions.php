<?php

require_once 'node/RootNode.php';
require_once 'node/TagNode.php';
require_once 'node/VoidTagNode.php';
require_once 'node/TextNode.php';
require_once 'node/MiscNode.php';
require_once 'node/WsNode.php';
require_once 'node/IfNode.php';
require_once 'node/ForNode.php';
require_once 'node/IncludeNode.php';

class DebugActions {

    public function tagStartAction($tagName, $attributes) {
        printf('start tag: %s' . PHP_EOL, $tagName);
    }

    public function tagEndAction($tagName) {
        printf('end tag: %s' . PHP_EOL, $tagName);
    }

    public function voidTagAction($tagName, $attributes, $hasSlashClose) {
        printf('void tag: %s' . PHP_EOL, $tagName);
    }

    public function staticElementAction($token) {
        printf('static: %s' . PHP_EOL, $token->text);
    }
}
