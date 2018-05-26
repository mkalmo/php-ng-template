<?php

class StartTag extends RegexToken {

    public function getTagName() {
        $start_tag = '/<(\w+)[^>\/]*>/';

        if (preg_match($start_tag, $this->contents, $matches)) {
            list ($whole_match, $tag_name) = $matches;
            return $tag_name;
        } else {
            throw new Error('can\'t get tag name from: ' + $this->contents);
        }
    }
}
