<?php

namespace tplLib;

function read_file($filePath) {
    if (is_dir($filePath)) {
        throw new \Error("$filePath is directory");
    }

    $contents = file_get_contents($filePath);

    if ($contents === FALSE) {
        throw new \Error("can't read file: $filePath");
    }

    return $contents;
}