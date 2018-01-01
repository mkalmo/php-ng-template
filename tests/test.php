<?php

$a = [1];
$b = [2];

$c = $b;

$b []= 3;

print_r($b);
print_r($c);


//require_once('../src/tiny-template.php');
//
//$data = [
//    '$flag1' => true,
//    '$flag2' => false,
//    '$title' => 'Hello',
//    '$fragment_path' => '../tpl/fragment2.html',
//    '$list' => [1, 2, 3]];
//
//echo render_template('../tpl/main.html', $data);
