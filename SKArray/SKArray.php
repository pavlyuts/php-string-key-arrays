<?php

/*
 * Array-like class uses string as keys and converts any given key to a string
 * 
 * (c) Alexey Pavlyuts
 */

namespace SKArray;

class SKArray implements \Iterator, \ArrayAccess, \Countable {

    protected $strict;
    protected $list = [];
    protected $index = 0;
    protected $indexArray = [];

    /**
     * Array-like class only accepts string as keys and converts any kay given 
     * to a string.
     * 
     * @param bool $strict default true and throws exception in a case if trying 
     *                     to access witn non-existing key. Set to false to made
     *                     it work more array-like: return NULL and generate PHP
     *                     notice.
     */
    public function __construct(bool $strict = true) {
        $this->strict = $strict;
    }

    public function offsetExists($offset): bool {
        return array_key_exists('S' . $offset, $this->list);
    }

    public function offsetGet($offset) {
        if (array_key_exists('S' . $offset, $this->list)) {
            return $this->list['S' . $offset];
        }
        if ($this->strict) {
            throw new SKArrayException("Got undefined index '$offset'");
        }
        trigger_error("SKArray got undefined index '$offset'", E_USER_NOTICE);
        return null;
    }

    public function offsetSet($offset, $value): void {
        if ((null === $offset) || '' === $offset) {
            throw new SKArrayException("Use null or empty string as a key is prohibited");
        }
        $this->list['S' . $offset] = $value;
    }

    public function offsetUnset($offset): void {
        unset($this->list['S' . $offset]);
    }

    public function count(): int {
        return count($this->list);
    }

    public function current() {
        return $this->list[$this->indexArray[$this->index]];
    }

    public function key() {
        return substr($this->indexArray[$this->index], 1);
    }

    public function next(): void {
        $this->index++;
    }

    public function rewind(): void {
        $this->index = 0;
        $this->indexArray = array_keys($this->list);
    }

    public function valid(): bool {
        return isset($this->indexArray[$this->index]) && isset($this->list[$this->indexArray[$this->index]]);
    }

}
