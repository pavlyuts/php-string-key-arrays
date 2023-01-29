<?php

/*
 * Array-like class uses string as keys and converts any given key to a string
 * 
 * (c) Alexey Pavlyuts
 */

namespace SKArrayTests;

/**
 * Class for testing
 *
 */
class Elem {

    protected $prop;

    public function __construct($val) {
        $this->prop = $val;
    }

}
