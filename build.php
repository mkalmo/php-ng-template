<?php

require_once('src/concat.php');

if (!file_exists('dist')) {
    mkdir('dist');
}


$contents = '// from: https://bitbucket.org/mkalmo/php-template' . PHP_EOL;
$contents .= '<?php ' . PHP_EOL;
$contents .= concatenatePhpFiles('src/tpl.php');

file_put_contents('dist/tpl.php', '<?php ' . $contents);
