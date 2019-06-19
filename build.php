<?php

require_once 'src/BundleBuilder.php';

$rootFile = 'src/tpl.php';

$builder = new BundleBuilder($rootFile);

$content = $builder->build();

$dir = "./dist";

if (!is_dir($dir)) {
    mkdir($dir);
}

file_put_contents("$dir/tpl.php", "<?php\n" . $content);
