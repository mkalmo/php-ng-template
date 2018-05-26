<?php

class EndTag extends RegexToken {

    public function getTagName() {
        $end_tag = '/<\/(\w+)[^>\/]*>/';

        if (preg_match($end_tag, $this->contents, $matches)) {
            list ($whole_match, $tag_name) = $matches;
            return $tag_name;
        } else {
            throw new Error('can\'t get tag name from: ' . $this->contents);
        }
    }
}
