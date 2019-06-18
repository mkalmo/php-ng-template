<?php

require_once(__DIR__ . '/../vendor/simpletest/simpletest/unit_tester.php');

class ExtendedTestCase extends UnitTestCase {

    public function getTests() {
        $class = get_class($this);

        $r = new ReflectionClass($class);

        $testMethods = array_filter($r->getMethods(), function ($each) use ($class) {
            return $each->class === $class
                && $each->isPublic()
                && !$each->isStatic()
                && !preg_match('/^x_/', $each->name);
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

    protected function assertListEqual($expectedList, $actualList) {
        $actualListCopy = $actualList;
        $pos = 0;
        foreach ($expectedList as $expectedValue) {
            $actualValue = array_shift($actualListCopy);
            if ($expectedValue !== $actualValue) {
                $this->fail(sprintf('expected: %s actual: %s pos: %s',
                    $expectedValue, $actualValue, $pos));
            }
            $pos++;
        }

        $this->assertEqual(count($expectedList), count($actualList), 'Different lengths: %s');
    }



}
