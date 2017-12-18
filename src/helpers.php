<?php

function array_find($array, $predicate) {
    $list = array_values(array_filter($array, $predicate));
    if (sizeof($list) > 1) {
        throw new UnexpectedValueException("found more than one");
    }

    return sizeof($list) == 0 ? NULL : $list[0];
}
