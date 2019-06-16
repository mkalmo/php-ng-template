<?php

require_once('ExtendedTextCase.php');
require_once('../src/BundleBuilder.php');

class BundleBuilderTests extends ExtendedTestCase {

    function buildsBundleByIncludingDependencies() {

        $rootFile = 'test-data/bundle/root.php';

        $builder = new BundleBuilder($rootFile);

        $result = $builder->build();

        $result = preg_replace('/namespace/', '', $result);
        $result = preg_replace('/\s/', '', $result);
        $result = preg_replace('/[{}]/', '', $result);

        $this->assertEqual(
            "'1';'sub_sub_1';'sub_1';'sub_2';'root';", $result);
    }
}

!debug_backtrace() && (new BundleBuilderTests())->run(new TextReporter());
