<?php

require_once('../src/tiny-template.php');

$data = [
    '$flag1' => false,
    '$flag2' => false,
    '$title' => 'Hello',
    '$list' => [1, 2, 3]];

echo render_template('../tpl/main.html', $data);
