<?php

class Customer {
    public $name;
    public $friends = [];

    public function __construct($name) {
        $this->name = $name;
    }

    public function getFriends() {
        return $this->friends;
    }
}
