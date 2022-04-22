<?php

namespace ROrier\Bag\Tests\Components;

use PHPUnit\Framework\TestCase;
use ROrier\Bag\Components\Bag;

class BagTest extends TestCase
{
    public function testSimpleMerge()
    {
        $bag = new Bag();

        $this->assertCount(0, $bag->toArray(), "Parameters array must be empty.");
        $this->assertNull($bag['not-found'], "bag['not-found'] must be null.");

        $bag->merge(array('key' => 'value-1'));

        $this->assertCount(1, $bag->toArray(), "Parameters array must contains 1 entry.");
        $this->assertNull($bag['not-found'], "bag['not-found'] must be null.");
        $this->assertArrayHasKey('key', $bag->toArray(), "Parameters array must contains entry 'key'.");
        $this->assertEquals('value-1', $bag->toArray()['key'], "'key' entry value must be equals to 'value-1'.");
        $this->assertEquals('value-1', $bag['key'], "'key' entry value must be equals to 'value-1'.");

        $bag->merge(array('key' => 'value-2'));

        $this->assertCount(1, $bag->toArray(), "Parameters array must contains 1 entry.");
        $this->assertNull($bag['not-found'], "bag['not-found'] must be null.");
        $this->assertArrayHasKey('key', $bag->toArray(), "Parameters array must contains entry 'key'.");
        $this->assertEquals('value-2', $bag->toArray()['key'], "'key' entry value must be equals to 'value-2'.");
        $this->assertEquals('value-2', $bag['key'], "'key' entry value must be equals to 'value-2'.");
    }

    public function testAssociativeMerge()
    {
        $bag = new Bag();

        $this->assertCount(0, $bag->toArray(), "Parameters array must be empty.");

        $bag->merge(array('key' => array(
            'key-1' => 'value-1',
            'key-2' => 'value-2'
        )));

        $this->assertCount(2, $bag['key'], "Parameters array must contains 1 entry.");
        $this->assertArrayHasKey('key-1', $bag['key'], "Parameters array must contains entry 'key-1'.");
        $this->assertArrayHasKey('key-2', $bag['key'], "Parameters array must contains entry 'key-2'.");
        $this->assertEquals('value-1', $bag['key.key-1'], "'key-1' entry value must be equals to 'value-1'.");
        $this->assertEquals('value-2', $bag['key.key-2'], "'key-2' entry value must be equals to 'value-2'.");

        $bag->merge(array('key' => array(
            'key-2' => 'value-2-bis',
            'key-3' => 'value-3'
        )));

        $this->assertCount(3, $bag['key'], "Parameters array must contains 1 entry.");
        $this->assertArrayHasKey('key-1', $bag['key'], "Parameters array must contains entry 'key-1'.");
        $this->assertArrayHasKey('key-2', $bag['key'], "Parameters array must contains entry 'key-2'.");
        $this->assertArrayHasKey('key-3', $bag['key'], "Parameters array must contains entry 'key-3'.");
        $this->assertEquals('value-1', $bag['key.key-1'], "'key-1' entry value must be equals to 'value-1'.");
        $this->assertEquals('value-2-bis', $bag['key.key-2'], "'key-2' entry value must be equals to 'value-2-bis'.");
        $this->assertEquals('value-3', $bag['key.key-3'], "'key-3' entry value must be equals to 'value-3'.");
    }

    public function testOverrideMerge()
    {
        $bag = new Bag();

        $this->assertCount(0, $bag->toArray(), "Parameters array must be empty.");

        $bag->merge(array('key' => array(
            'key-1' => 'value-1',
            'key-2' => 'value-2'
        )));

        $this->assertCount(2, $bag['key'], "Parameters array must contains 1 entry.");
        $this->assertArrayHasKey('key-1', $bag['key'], "Parameters array must contains entry 'key-1'.");
        $this->assertArrayHasKey('key-2', $bag['key'], "Parameters array must contains entry 'key-2'.");
        $this->assertEquals('value-1', $bag['key.key-1'], "'key-1' entry value must be equals to 'value-1'.");
        $this->assertEquals('value-2', $bag['key.key-2'], "'key-2' entry value must be equals to 'value-2'.");

        $bag->merge(array('!key' => array(
            'key-2' => 'value-2-bis',
            'key-3' => 'value-3'
        )));

        $this->assertCount(2, $bag['key'], "Parameters array must contains 1 entry.");
        $this->assertArrayNotHasKey('key-1', $bag['key'], "Parameters array must not contains entry 'key-1'.");
        $this->assertArrayHasKey('key-2', $bag['key'], "Parameters array must contains entry 'key-2'.");
        $this->assertArrayHasKey('key-3', $bag['key'], "Parameters array must contains entry 'key-3'.");
        $this->assertEquals('value-2-bis', $bag['key.key-2'], "'key-2' entry value must be equals to 'value-2-bis'.");
        $this->assertEquals('value-3', $bag['key.key-3'], "'key-3' entry value must be equals to 'value-3'.");
    }
    
    public function testSymlinks()
    {
        $bag = new Bag();

        $this->assertCount(0, $bag->toArray(), "Parameters array must be empty.");

        $bag->merge(array(
            'key' => array(
                'key-1' => 'value-1',
                'key-2' => array(
                    'sub-key-1' => 'sub-value-1',
                    'sub-key-2' => 'sub-value-2'
                )
            ),
            'sym' => array(
                'link-1' => '=key',
                'link-2' => '=key.key-2'
            )
        ));

        $this->assertIsArray($bag['sym.link-1'], "bag['sym.link-1'] must be an array.");
        $this->assertIsArray($bag->toArray()['sym']['link-1'], "bag->toArray()[sym][link-1] must be an array.");
        $this->assertEquals('value-1', $bag['sym.link-1.key-1'], "bag['sym.link-1.key-1'] must be equals to 'value-1'.");
        $this->assertEquals('value-1', $bag->toArray()['sym']['link-1']['key-1'], "bag->toArray()[sym][link-1]['key-1'] must be equals to 'value-1'.");
        $this->assertIsArray($bag['sym.link-2'], "bag['sym.link-2'] must be an array.");
        $this->assertIsArray($bag->toArray()['sym']['link-2'], "bag->toArray()[sym][link-2] must be an array.");
        $this->assertEquals('sub-value-1', $bag['sym.link-2.sub-key-1'], "bag['sym.link-2.sub-key-1'] must be equals to 'sub-value-1'.");
        $this->assertEquals('sub-value-1', $bag->toArray()['sym']['link-2']['sub-key-1'], "bag->toArray()[sym][link-2]['sub-key-1'] must be equals to 'sub-value-1'.");

    }
}
