<?php

function concatenatePhpFiles($filePath) {
    $buffer = [];
    $lines = file($filePath);
    foreach ($lines as $line) {

        if (strpos($line, '<?php') !== false) {
            continue;
        }

        if (preg_match('/require_once \'([^\']+)\'/', $line, $matches)) {
            list($all, $fileSubPath) = $matches;
            $pathStart = getPath($filePath);
            $pathEnd = getPath($fileSubPath);
            $fileName = getFileName($fileSubPath);

            $newFilePath = createFilePath($pathStart, $pathEnd, $fileName);
            $buffer[] = concatenatePhpFiles($newFilePath);
        } else {
            $buffer[] = $line;
        }
    }

    return join('', $buffer);
}

function createFilePath($dirStart, $dirEnd, $fileName) {
    $filePath = '';
    if ($dirStart) {
        $filePath .= "$dirStart/";
    }
    if ($dirEnd) {
        $filePath .= "$dirEnd/";
    }

    return $filePath . $fileName;
}

function getPath($path) {
    return pathinfo($path)['dirname'];
}

function getFileName($path) {
    return pathinfo($path)['basename'];
}
