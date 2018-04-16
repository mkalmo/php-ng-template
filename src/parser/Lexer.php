<?php

require_once 'Token.php';

class Lexer {

    public function tokenize($html) {
        $start_tag = '<[^>\/]+>';
        $end_tag = '<\/[^>]+>';
        $text = '(?<=>)[^<]+';

        preg_match_all("/$start_tag|$end_tag|$text/", $html, $matches);

        return array_map('self::toTag', $matches[0]);
    }

    private static function toTag($chunk) {
        $start_tag = '/<(\w+)[^>\/]*>/';
        $end_tag_name = '/<\/(\w+)/';

        if (preg_match($start_tag, $chunk, $matches)) {
            list ($whole_tag, $tag_name) = $matches;
            return new StartTag($tag_name, $whole_tag);
        } else if (preg_match($end_tag_name, $chunk, $matches)) {
            list ($whole_match, $tag_name) = $matches;
            return new EndTag($tag_name);
        } else {
            return new TextToken($chunk);
        }
    }
}
