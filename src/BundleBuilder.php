<?php

class BundleBuilder {

    private $rootPath;
    private $alreadySeen = [];

    public function __construct($rootPath) {
        $this->rootPath = $rootPath;
    }

    public function build() {
        $rootNode = $this->createFileNode($this->rootPath);

        return $rootNode->__toString();
    }

    function createFileNode($filePath) {
        $this->alreadySeen[] = realpath($filePath);

        $content = [];
        $dependencies = [];
        $namespace = '';
        foreach (file($filePath) as $line) {
            if (trim($line) === '<?php') {
                continue;
            }

            if ($this->isNamespaceLine($line)) {
                $namespace = $this->getNamespace($line);
                continue;
            }

            $tmp = trim($line);
            if ($this->isRequireLine($tmp)) {
                $dependencies[] = $this->getRequirePath($tmp, $filePath);
            } else {
                $content[] = $line;
            }
        }

        $node = new BundleFileNode(join('', $content), $namespace);

        foreach ($dependencies as $dependency) {
            $dir = dirname($filePath);

            $dependencyPath = preg_match('!^/!', $dependency)
                ? $dependency
                : $this->concatPath($dir, $dependency);

            if (!in_array(realpath($dependencyPath), $this->alreadySeen)) {
                $childNode = $this->createFileNode($dependencyPath);

                $node->dependencies[] = $childNode;
            }
        }

        return $node;
    }

    function concatPath($root, $relative) {
        return join('/', [$root, $relative]);
    }

    function isRequireLine($string) {
        return preg_match('/^require_once/', $string);
    }

    function isNamespaceLine($string) {
        return preg_match('/^namespace/', $string);
    }

    function getNamespace($string) {
        $string = preg_replace('/^namespace/', '', $string);
        $string = trim($string);
        $string = preg_replace('/;$/', '', $string);
        return $string;
    }

    function getRequirePath($string, $parentPath) {
        $string = preg_replace('/^require_once/', '', trim($string));

        $string = preg_replace(
            '/__FILE__/',
            "'" . realpath($parentPath) . "'",
            $string);

        $path = '';

        $string = '$path = ' . $string;

        eval($string);

        return $path;
    }

}

class BundleFileNode {
    public $dependencies = [];
    public $content;
    public $namespace;
    public function __construct($content, $namespace) {
        $this->content = $content;
        $this->namespace = $namespace;
    }

    public function __toString() {
        $lines = [];
        foreach ($this->dependencies as $dependency) {
            $lines[] = $dependency->__toString();
        }

        $lines[] = sprintf("namespace %s {\n%s\n}\n", $this->namespace, $this->content);

        return join('', $lines);
    }
}
