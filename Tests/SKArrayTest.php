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

        $this->assertEquals(['586', 'aaa', '777', '5.15', 'bbb'], $a->array_keys());
        $this->assertEquals(['Number', 'Text', 'NumberString', 'Float', 'MoreText'], $a->array_values());

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

    public function testColumnArray() {
        $a = new SKArray();
        $t = [
            'r1' => ['a' => 1, 'b' => 2, 7 => 3, 'c' => 4],
            'r2' => ['a' => 5, 'b' => 6, 7 => 7],
            'r3' => ['a' => 9, 'b' => 10, 7 => 11, 'c' => 12],
        ];
        foreach ($t as $key => $val) {
            $a[$key] = $val;
        }
        $a['scalar'] = 'StringValue';

        $r = $a->column('a');
        $this->assertTrue($r instanceof SKArray);
        $this->assertEquals(array_keys($t), $r->array_keys());
        $this->assertEquals(array_column($t, 'a'), $r->array_values());

        $r = $a->column(7);
        $this->assertTrue($r instanceof SKArray);
        $this->assertEquals(array_keys($t), $r->array_keys());
        $this->assertEquals(array_column($t, 7), $r->array_values());

        $r = $a->column('c');
        $this->assertTrue($r instanceof SKArray);
        $this->assertEquals(['r1', 'r3'], $r->array_keys());
        $this->assertEquals([4, 12], $r->array_values());

        $r = $a->column('d');
        $this->assertTrue($r instanceof SKArray);
        $this->assertEquals(0, count($r));
    }

    public function testSetSubarrayItem() {
        $a = new SKArray();
        $a->setSubarrayItem('test1', 'test1Value');
        $this->assertEquals(['test1Value'], $a['test1']);
        $a->setSubarrayItem('test1', 'test2Value');
        $this->assertEquals(['test1Value', 'test2Value'], $a['test1']);
        $a->setSubarrayItem('test1', 'test3Value', 'test3Key');
        $this->assertEquals(['test1Value', 'test2Value', 'test3Key' => 'test3Value'], $a['test1']);

        $a['test2'] = 'TestStringValue';
        $this->assertEquals('TestStringValue', $a['test2']);
        $a->setSubarrayItem('test2', 'test2Value');
        $this->assertEquals(['test2Value'], $a['test2']);
    }

    public function testColumnObjcts() {
        $a = new SKArray();
        $a['E1'] = new Elem('VE1');
        $a['EP1'] = new ElemProp('VEP1');
        $a['EM1'] = new ElemMethod('VEM1');
        $a['E2'] = new Elem('VE2');
        $a['EP2'] = new ElemProp('VEP2');
        $a['EM2'] = new ElemMethod('VEM2');

        $r = $a->column('prop');
        $this->assertEquals(['EP1', 'EP2'], $r->array_keys());
        $this->assertEquals(['VEP1', 'VEP2'], $r->array_values());

        $r = $a->column('method');
        $this->assertEquals(['EM1', 'EM2'], $r->array_keys());
        $this->assertEquals(['VEM1', 'VEM2'], $r->array_values());

        $r = $a->column('proMethod');

        $r = $a->column('process', 'reverse');
        $this->assertEquals(['1MEV', '2MEV'], $r->array_values());

        $r = $a->column('process', 'add', 'bis');
        $this->assertEquals(['VEM1bis', 'VEM2bis'], $r->array_values());
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
        $a['BadIndex'];
    }

    public function testNotice() {
        $a = new SKArray(false);
        $this->assertNull(@$a['BadIndex']);
        $this->expectNotice();
        $this->expectNoticeMessage('BadIndex');
        $this->assertNull($a['BadIndex']);
    }

}
