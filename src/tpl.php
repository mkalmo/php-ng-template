<?php

require_once 'parser/FileParser.php';
require_once 'Scope.php';

function render_template($templatePath, $data = []) {

    try {
        $tree = (new tplLib\FileParser($templatePath))->parse();

        return $tree->render(new tplLib\Scope($data,
            realpath(dirname($templatePath))));

    } catch (Exception $e) {
        printf('<pre>%s%s%s</pre>', PHP_EOL, $e->getMessage(), PHP_EOL);
    }
}
