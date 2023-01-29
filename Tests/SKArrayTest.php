<?php

/*
 * Array-like class uses string as keys and converts any given key to a string
 * 
 * (c) Alexey Pavlyuts
 */

namespace SKArrayTests;

use SKArray\SKArray;
use SKArray\SKArrayException;

/**
 * Test class for StringArray
 *
 */
class SKArrayTest extends \PHPUnit\Framework\TestCase {

    public function testSKArrayMain() {
        $test = [
            ['key' => 586, 'val' => 'Number',],
            ['key' => 'aaa', 'val' => 'Text',],
            ['key' => '777', 'val' => 'NumberString',],
            ['key' => 5.15, 'val' => 'Float',],
            ['key' => 'bbb', 'val' => 'MoreText',],
        ];

        $a = new SKArray();
        foreach ($test as $rec) {
            $a[$rec['key']] = $rec['val'];
        }
        $this->assertEquals(5, count($a));
        $i = 0;
        foreach ($a as $key => $val) {
            $this->assertIsString($key);
            $this->assertEquals($key, $test[$i]['key']);
            $this->assertEquals($val, $test[$i++]['val']);
        }

        unset($a[777]);
        $this->assertEquals(4, count($a));

        foreach ($a as $key => $val) {
            unset($a[$key]);
        }
        $this->assertEquals(0, count($a));

        $a['789'] = 'assign1';
        $a[789] = 'assign2';
        $this->assertEquals('assign2', $a[789]);
        $this->assertEquals('assign2', $a['789']);

        $this->assertTrue(isset($a['789']));
        $this->assertFalse(isset($a['Unknown']));
        
        $this->assertNull($a['Unknown'] ?? null);
    }

    public function testException_1() {
        $a = new SKArray();
        $this->expectException(SKArrayException::class);
        $a[] = 'value';
    }

    public function testException_2() {
        $a = new SKArray();
        $this->expectException(SKArrayException::class);
        $a[''] = 'value';
    }

    public function testException_3() {
        $a = new SKArray();
        $this->expectException(SKArrayException::class);
        $a['value'];
    }

    public function testNotice() {
        $a = new SKArray(false);
        $this->assertNull($a['value']);
    }

}
