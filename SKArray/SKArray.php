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
        return array_key_exists($this->encodeOffset($offset), $this->list);
    }

    public function offsetGet($offset) {
        $realOffset = $this->encodeOffset($offset);
        if (array_key_exists($realOffset, $this->list)) {
            return $this->list[$realOffset];
        }
        if ($this->strict) {
            throw new SKArrayException("Got undefined index '$offset'");
        }
        trigger_error("SKArray: Got undefined index '$offset'", E_USER_NOTICE);
        return null;
    }

    public function offsetSet($offset, $value): void {
        $this->list[$this->encodeOffset($offset)] = $value;
    }

    public function offsetUnset($offset): void {
        unset($this->list[$this->encodeOffset($offset)]);
    }

    public function count(): int {
        return count($this->list);
    }

    public function current() {
        return $this->list[$this->indexArray[$this->index]];
    }

    public function key() {
        return $this->decodeOffset($this->indexArray[$this->index]);
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

    /**
     * Synonym for $this->keys();
     * Same as array_keys(), return all the keys in a list
     * 
     * @return array - all keys, array of string
     */
    public function array_keys(): array {
        return $this->keys();
    }

    /**
     * Same as array_keys(), return all the keys in a list
     * 
     * @return array - all keys, array of string
     */
    public function keys(): array {
        $result = [];
        foreach (array_keys($this->list) as $key) {
            $result[] = $this->decodeOffset($key);
        }
        return $result;
    }

    /**
     * Synonym for $this->values();
     * same as array_values(), return all values in a list
     * 
     * @return array - all values, array of mixed
     */
    public function array_values(): array {
        return $this->values();
    }

    /**
     * same as array_values(), return all values in a list
     * 
     * @return array - all values, array of mixed
     */
    public function values(): array {
        return array_values($this->list);
    }

    /**
     * About the same like array_column but... a bit different!
     * 
     * If an element is an array, it will try to retrive array value by key $column.
     * 
     * If an element is_object and property $column exist, it will try to get it
     * 
     * If an element is_object and method $column exist, it will try to call it 
     * unpacking $args, like $element->$column(...$args);
     * 
     * @param mixed $column array key, property or methid name
     * @param mixed multiple $args
     * @return SKArray of results of successful calll with same keys
     */
    public function column($column, ...$args): SKArray {
        $result = $this->createSelf();
        foreach ($this->list as $key => $element) {
            if (null !== ($answer = $this->getElementColumn($element, $column, $args))) {
                $result[$this->decodeOffset($key)] = $answer;
            }
        }
        return $result;
    }

    protected function getElementColumn($element, $column, $args) {
        if (is_array($element)) {
            return $element[$column] ?? null;
        }
        if (is_object($element) && is_string($column)) {
            return $this->getFromObject($element, $column, $args);
        }
        return null;
    }

    protected function getFromObject($element, $column, $args) {
        try {
            if (property_exists($element, $column)) {
                return $element->$column;
            }
            if (method_exists($element, $column)) {
                return $element->$column(...$args);
            }
        } catch (\Error $ex) {
            $className = get_class($element);
            trigger_error("SKArray: When access propery or method '$column' of elment class '$className' got error'{$ex->getMessage()}'", E_USER_NOTICE);
            return null;
        }
    }

    protected function createSelf() {
        $cl = static::class;
        return new $cl($this->strict);
    }

    protected function encodeOffset($offset) {
        if (!(is_string($offset) || is_int($offset) || is_float($offset))) {
            throw new SKArrayException("Only string, int and float types allowed as a key");
        }
        if (is_null($offset) || '' === $offset) {
            throw new SKArrayException("Use null or empty string as a key is prohibited");
        }
        return 'S' . $offset;
    }

    protected function decodeOffset($offset) {
        return substr($offset, 1);
    }

}
