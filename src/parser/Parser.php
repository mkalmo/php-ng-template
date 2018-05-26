<?php

require_once 'RegexLexer.php';
require_once 'token/RootStartTag.php';
require_once 'token/RootEndTag.php';
require_once 'node/Node.php';
require_once 'NodeFactory.php';

class Parser {

    public function parse($html) {
        $tokens = (new RegexLexer())->tokenize($html);

        array_push($tokens, new RootEndTag());
        array_unshift($tokens, new RootStartTag());

        return self::build($tokens, 0)[0];
    }

    private static function build(&$tokens, $level) {

        // kuni on sisu
        // kogu sisu

        $contents = [];

        $current_token = array_shift($tokens);

//        print "before: $level - $current_token\n";

        while (static::isContent(static::peekNext($tokens))) {
            $contents = array_merge($contents, self::build($tokens, $level + 1));
        }

        // sisu kogutud.
        // 1. my end tag
        // 2. other end tag or root end tag

//        print "after: $level - $current_token\n";
//        $next = static::peekNext($tokens);
//        print "next: $level - $next\n";

        if ($current_token->isMyEndTag(static::peekNext($tokens))) {
            array_shift($tokens);
            $node = (new NodeFactory())->createNode($current_token);
            $node->addChildren($contents);
            return [$node];

        } else { // other end tag
            $node = (new NodeFactory())->createNode($current_token);
            return array_merge([$node], $contents);
        }

    }

    private static function peekNext($list) {
        return isset($list[0]) ? $list[0] : null;
    }

    private static function isContent($token) {
        return $token !== null && !$token->isEndTag();
    }

}