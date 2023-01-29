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
class ElemMethod extends Elem {

    public function method() {
        return $this->prop;
    }

    protected function proMethod() {
        
    }

    public function process(string $oper, $add = '') {
        switch ($oper) {
            case 'add':
                return $this->prop . $add;
            case 'reverse': 
                return strrev($this->prop);
            default :
                return $this->prop;
        }
    }

}
