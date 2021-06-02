<?php

namespace EV\CopyBundle\Memory;

class MatchingMemory {

    protected $matchings = array();

    public function __construct() {

    }

    public function addMatching($name, $originalObject, $copyObject) {
        $this->matchings[$name][spl_object_hash($originalObject)] = $copyObject;
    }

    public function getMatchingCopy($name, $originalObject) {
        return $this->matchings[$name][spl_object_hash($originalObject)];
    }

    public function getMatchings() {
        return $this->matchings;
    }

    public function clear() {
        $this->matchings = array();
    }

}
