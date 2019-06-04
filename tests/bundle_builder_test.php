<?php

require_once('ExtendedTextCase.php');
require_once('../src/BundleBuilder.php');

class BundleBuilderTests extends ExtendedTestCase {

    function buildsBundleByIncludingDependencies() {

        $rootFile = 'test-data/bundle/root.php';

        $builder = new BundleBuilder($rootFile);

        $this->assertEqual(
            "'1';'sub_sub_1';'sub_1';'sub_2';'sub_sub_1';'root';",
            $builder->build());
    }
}

!debug_backtrace() && (new BundleBuilderTests())->run(new TextReporter());
