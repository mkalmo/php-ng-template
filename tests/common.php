<?php

require_once(__DIR__ . '/../vendor/simpletest/simpletest/unit_tester.php');

class ExtendedTestCase extends UnitTestCase {

    public function getTests() {
        $class = get_class($this);

        $r = new ReflectionClass($class);

        $testMethods = array_filter($r->getMethods(), function ($each) use ($class) {
            return $each->class === $class && $each->isPublic();
        });

        $methodNames = array_map(
            function ($each) {
                return $each->name;
            }, $testMethods);

        $selected = array_filter($methodNames, function ($each) {
            return preg_match('/^_/', $each);
        });

        if ($selected) {
            return $selected;
        }

        return $methodNames;
    }
}
