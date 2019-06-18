<?php

namespace tplLib;

class NopActions {

    public function tagStartAction($tagName, $attributes) {
    }

    public function tagEndAction($tagName) {
    }

    public function voidTagAction($tagName, $attributes, $hasSlashClose) {
    }

    public function staticElementAction($token) {
    }
}
