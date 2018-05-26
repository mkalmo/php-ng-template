<?php

require_once 'Scope.php';

function render_template($templatePath, $data = []) {
    $node = new \DOMDocument();
    $node->loadHTMLFile($templatePath);

    tpl\traverse($node, new Scope($data));

    return $node->saveHTML();
}
