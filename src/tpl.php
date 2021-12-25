<?php

require_once 'parser/FileParser.php';
require_once 'Scope.php';

function renderTemplate($templatePath, $data = [], $translations = []) {

    try {
        $tree = (new tplLib\FileParser($templatePath))->parse();

        return $tree->render(new tplLib\Scope($data, $translations,
            realpath(dirname($templatePath))));

    } catch (Exception $e) {
        error_log($e->getMessage());

        return sprintf('<pre>%s%s%s</pre>', PHP_EOL, $e->getMessage(), PHP_EOL);
    }
}
