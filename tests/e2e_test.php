<?php

require_once('ExtendedTextCase.php');
require_once('../src/tpl2.php');


class E2eTests extends ExtendedTestCase {

    function abc() {
        print render_template('../tpl/e2e.html', ['$flag' => true]);
    }

}

(new E2eTests())->run(new TextReporter());