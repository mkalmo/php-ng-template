<?php

namespace tplLib;

function loadContents($filePath) {
    if (is_dir($filePath)) {
        throw new \Exception("$filePath is directory");
    }

    $contents = file_get_contents($filePath);

    if ($contents === false) {
        throw new \Exception("can't read file: $filePath");
    }

    return $contents;
}