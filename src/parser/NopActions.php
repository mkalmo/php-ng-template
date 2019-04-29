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

class NopActions {

    public function tagStartAction($tagName, $attributes) {
    }

    public function tagEndAction($tagName) {
    }

    public function voidTagAction($tagName, $attributes, $hasSlashClose) {
    }

    public function staticElementAction($token) {
    }
}
