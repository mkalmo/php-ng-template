<?php

require_once 'token/RegexToken.php';
require_once 'token/StartTag.php';
require_once 'token/EndTag.php';
require_once 'token/TextRegexToken.php';

class RegexLexer {

    public function tokenize($html) {
        $start_tag = '<[^>\/]+>';
        $end_tag = '<\/[^>]+>';
        $text = '(?<=>)[^<]+';

        preg_match_all("/$start_tag|$end_tag|$text/", $html, $matches);

        return array_map('self::toTag', $matches[0]);
    }

    private static function toTag($chunk) {
        $start_tag = '/<[^>\/]+>/';
        $end_tag = '/<\/[^>]+>/';

        if (preg_match($start_tag, $chunk)) {
            return new StartTag($chunk);
        } if (preg_match($end_tag, $chunk)) {
            return new EndTag($chunk);
        } else {
            return new TextRegexToken($chunk);
        }
    }
}
