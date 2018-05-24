<?php

require_once '../src/parser/Parser.php';
require_once '../src/Scope.php';

use tpl\Scope;

function render_template($templatePath, $data = []) {

    $html = file_get_contents($templatePath);

    $node = (new Parser())->parse($html);

    var_dump($node);

    return $node->render(new Scope($data));
}
