<?php

# require_once('../src/tiny-template.php');
require_once('../tpl.phar');

$data = [
    '$flag1' => true,
    '$flag2' => false,
    '$title' => 'Hello',
    '$list' => [1, 2, 3]];

echo render_template('../tpl/main.html', $data);
