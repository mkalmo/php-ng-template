<?php


$data['var1'] = 3;
$data['list'] = [1, 2, 3];

var_dump(evaluate('join(", ", $list)', $data));

function evaluate($expression, $data) {

    foreach ($data as $key => $value) {
        ${ $key } = $value;
    }

    return eval('return ' . $expression . ';');
}