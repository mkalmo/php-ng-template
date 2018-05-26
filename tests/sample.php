<?php

require_once('../dist/tpl.php');

$data = [
    'flag1' => true,
    'flag2' => false,
    'title' => 'Hello',
    'fragment_path' => '../data/templates/fragment2.html',
    'list' => [1, 2, 3]];

echo render_template('../data/templates/main.html', $data);
