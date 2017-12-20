<?php

$srcRoot = __DIR__ . "/src";
$buildRoot = __DIR__;

$phar = new Phar("$buildRoot/tpl.phar",
    FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::KEY_AS_FILENAME);

$phar->buildFromIterator(
    new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($srcRoot, FilesystemIterator::SKIP_DOTS)
    ),
    $srcRoot);

$phar->setStub($phar->createDefaultStub('tiny-template.php'));