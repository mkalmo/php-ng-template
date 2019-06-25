<?php

require_once '../src/tpl.php';
//require_once '../dist/tpl.php';

$templatePath = '../tests/test-data/tpl/main.html';

$data = [
    'title1' => 't1',
    'title2' => 't2',
    'flag1' => true,
    'flag2' => false,
    'cssClass' => 'menu',
    'list1' => [1, 2],
    'menuItems' => [1, 2],
    'contentPath' => 'content.html',
];

print renderTemplate($templatePath, $data);