<?php

class Token {
    public function isStartTag() {
        return get_class($this) === StartTag::class;
    }

    public function isEndTag() {
        return get_class($this) === EndTag::class;
    }

    public function isTextToken() {
        return get_class($this) === TextToken::class;
    }
}

class Tag extends Token {
    protected $name;

    public function __construct($name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }
}

class StartTag extends Tag {

    private $wholeTag;

    public function __construct($name, $wholeTag) {
        $this->name = $name;
        $this->wholeTag = $wholeTag;
    }

    public function getWholeTag() {
        return $this->wholeTag;
    }

    public function __toString() {
        return sprintf("<%s>\n", $this->getName());
    }
}

class EndTag extends Tag {
    public function __toString() {
        return sprintf("</%s>\n", $this->getName());
    }
}

class TextToken extends Token {
    private $contents;

    public function __construct($contents) {
        $this->contents = $contents;
    }

    public function getWholeTag() {
        return $this->contents;
    }

    public function getName() {
        return '';
    }
}