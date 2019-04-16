<?php

namespace Citadel\tests;

use Citadel\server\Util;
use PHPUnit\Framework\TestCase;

class UtilTest extends TestCase {

    public function testArrayExtract(){
        $array = range("A", "Z");
        $extracted_array = Util::array_extract($array, 7, 11);
        $this->assertEquals(range("H", "K"), $extracted_array);

        $array = range("A", "Z");
        $extracted_array = Util::array_extract($array, -1, -1);
        $this->assertEquals($array, $extracted_array);
    }

    public function testBits2int(){
        $bits = [1, 0, 1, 1, 0, 0, 0, 0];
        $int = Util::bits2int(...$bits);
        $this->assertEquals(176, $int);
    }

    public function testBytes2bits(){
        $bytes = [0, 240, 15, 255, 170];
        $bits = Util::bytes2bits(...$bytes);
        $this->assertEquals([0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 1, 0, 1, 0, 1, 0], $bits);
    }

    public function testBits2bytes(){
        $bits = [0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 1, 0, 1, 0, 1, 0];
        $bytes = Util::bits2bytes(...$bits);
        $this->assertEquals([0, 240, 15, 255, 170], $bytes);
    }

    public function testInt2bits(){
        $int = 176;
        $bits = Util::int2bits($int, 16);
        $this->assertEquals([0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 1, 1, 0, 0, 0, 0], $bits);
    }

}