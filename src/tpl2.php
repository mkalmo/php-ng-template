<?php

require_once 'parser/FileParser.php';
require_once 'Scope.php';

function render_template($templatePath, $data = []) {

    $tree = (new tplLib\FileParser($templatePath))->parse();

    return $tree->render(new tplLib\Scope($data, realpath(dirname($templatePath))));
}

