<?php

require_once('../src/tpl.php');

$data = [
    '$flag1' => true,
    '$flag2' => false,
    '$title' => 'Hello',
    '$fragment_path' => '../tpl/fragment2.html',
    '$list' => [1, 2, 3]];

echo render_template('../tpl/main.html', $data);
