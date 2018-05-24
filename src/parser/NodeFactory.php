<?php

require_once 'node/Node.php';
require_once 'node/IfNode.php';
require_once 'node/TextNode.php';

class NodeFactory {

    public function createNode($token) {

        if ($token->isTextToken()) {
            return new TextNode($token);
        }

        if (preg_match("/tpl-if/", $token->getContents())) {
            return new IfNode($token);
        } else {
            return new Node($token);
        }




    }

}
