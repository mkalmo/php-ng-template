<?php

abstract class RegexToken {

    protected $contents;

    public abstract function getTagName();

    public function isMyEndTag($token) {
        if (!$this->isStartTag()) {
            throw new Error('not start tag: ' . $this);
        }

        return $token->isEndTag()
            && $this->getTagName() === $token->getTagName();
    }

    public function __construct($content) {
        $this->contents = $content;
    }

    public function getContents() {
        return $this->contents;
    }

    public function isStartTag() {
        return get_class($this) === StartTag::class || $this->isRootStartTag();
    }

    public function isEndTag() {
        return get_class($this) === EndTag::class || $this->isRootEndTag();
    }

    public function isTextToken() {
        return get_class($this) === TextRegexToken::class;
    }

    public function isRootStartTag() {
        return get_class($this) === RootStartTag::class;
    }

    public function isRootEndTag() {
        return get_class($this) === RootEndTag::class;
    }

    public function __toString() {
        return $this->contents;
    }
}
