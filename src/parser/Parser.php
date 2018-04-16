<?php

require_once 'Lexer.php';
require_once 'Node.php';

class Parser {

    public function parse($html) {
        $tokens = (new Lexer())->tokenize($html);

        $root = new Node(null);

        if (count($tokens) === 0) {
            return $root;
        }

        $root->addChildren(self::build($tokens, 0));

        return $root;
    }

    private static function build(&$tokens, $level) {

        // kuni on sisu
        // kogu sisu

        $contents = [];

        $current_token = array_shift($tokens);

        while (static::isContent(static::peekNext($tokens))) {
            $contents = array_merge($contents, self::build($tokens, $level + 1));
        }


        // sisu kogutud.
        // 1. end
        // 2. my end tag
        // 3. other end tag

        if (static::peekNext($tokens) === null) {
            // content was same level stuff

            return array_merge([new Node($current_token->getWholeTag())], $contents);
        }

        $next_token = static::peekNext($tokens);
        $is_my_end = $next_token->isEndTag()
            && $next_token->getName() === $current_token->getName();

        if ($is_my_end) {

            array_shift($tokens);

            $node = new Node($current_token->getWholeTag());
            $node->addChildren($contents);
            return [$node];
        } else { // other end tag
            return array_merge([new Node($current_token->getWholeTag())], $contents);
        }
    }

    private static function noMoreTokens($tokens) {
        return count($tokens) === 0;
    }

    private static function peekNext($list) {
        return isset($list[0]) ? $list[0] : null;
    }

    private static function isContent($node) {
        return $node !== null && !$node->isEndTag();
    }

}