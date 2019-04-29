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

foreach (new DirectoryIterator('test-data/samples') as $fileInfo) {
    if($fileInfo->isDot()) continue;
    echo $fileInfo->getPathname() . PHP_EOL;
}