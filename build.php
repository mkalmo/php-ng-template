<?php

require_once 'src/BundleBuilder.php';

$rootFile = 'src/tpl2.php';

$builder = new BundleBuilder($rootFile);

$content = $builder->build();

file_put_contents('./dist/tpl.php', "<?php\n" . $content);
