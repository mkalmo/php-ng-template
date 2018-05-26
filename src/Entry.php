<?php

class Entry {
    public $key;
    public $value;

    public function __construct($name, $key) {
        $this->key = $name;
        $this->value = $key;
    }

    public function __toString() {
        return $this->key . "->" . $this->value;
    }
}
