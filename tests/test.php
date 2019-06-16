<?php

require_once('../src/tpl.php');
require_once('../src/Scope.php');
require_once('../src/Entry.php');

//$data = [
//    '$flag1' => true,
//    '$flag2' => false,
//    '$title' => 'Hello',
//    '$fragment_path' => '../tpl/fragment2.html',
//    '$list' => [1, 2, 3]];
//
//echo render_template('../tpl/main.html', $data);

//print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,2));

//$a = file('/home/mkalmo/git/php/php-template/tests/test-data/tpl/');

$d = '';

define('__FILE1__', '/home/mkalmo/tmp');

print eval("\$d = (dirname(__FILE1__) . '/../helpers.php');");

print $d;

