<?php

$html = '<body><p>p1</p></body>';

$start_tag = '<[^>\/]+>';
$end_tag = '<\/[^>]+>';
$text = '(?<=>)[^<]+';

preg_match_all("/$start_tag|$end_tag|$text/", $html, $matches);

# print_r($matches[0]);

$parts = [
    new Tag('body', false),
    new Tag('p', false),
    new Text('p1'),
    new Tag('p', true),
    new Tag('body', true)
];

$parts = [
    new Tag('body', false),
    new Text('text'),
    new Tag('body', true)
];

$active_start = [];

build_node($parts);

function build_node($list) {

    global $active_start;

    if (count($list) === 0) {
        return null;
    }

    $current = array_shift($list);

    if (get_class($current) === 'Tag' && !$current->isStart) {
        $active_start[] = new Node($current-name);

        $child_node = build_node($list);



    } else if (get_class($current) === 'Tag' && $current->isEnd) {

//        return new Node($active_start->name, $child_node);

    } else {

        return $current; // text node
    }

}

# loop

# kui on alguse tag
# panne current-iks
# töötle ülejäänut
# kui on lõpu tag
#   tee node valmis ja tagasta

