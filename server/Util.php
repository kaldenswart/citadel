<?php

namespace AllSeeingEye\server;

abstract class Util {

    public static function bytes2bits(int... $bytes){
        $bits = [];
        foreach($bytes as $byte){
            $bit_conversion = decbin($byte);
            $bit_count = strlen($bit_conversion);
            for($i = 0; $i < 8 - $bit_count; $i++){
                $bits []= 0;
            }
            for($i = 0; $i < $bit_count; $i++){
                $bits[]= (int)$bit_conversion[$i];
            }
        }
        return $bits;
    }

    public static function bits2bytes(int... $bits){
        $bytes = [];
        $chunks = sizeof($bits) / 8;
        for($i = 0; $i < $chunks; $i++){
            $bit_range = Util::array_extract($bits, $i * 8, (($i + 1) * 8));
            $bit_int = Util::bits2int(...$bit_range);
            $bytes[]= $bit_int;
        }
        return $bytes;
    }

    public static function array_extract(array $array, int $start = 0, int $length = -1){
        if($start < 0){
            $start = 0;
        }
        if($length < 0){
            $length = sizeof($array);
        }

        $extract = [];

        for($i = $start; $i < $length; $i++){
            $extract[]= $array[$i];
        }

        return $extract;
    }

    public static function bits2int(int... $bits){
        $string = "";
        foreach($bits as $bit){
            $string .= $bit;
        }
        return bindec($string);
    }

    public static function int2bits(int $int, int $padding = 0) : array{
        $bits = [];
        $bit_string = decbin($int);
        if(strlen($bit_string) < $padding){
            $padding_needed = $padding - strlen($bit_string);
            for($i = 0; $i < $padding_needed; $i++){
                $bits []= 0;
            }
        }
        for($i = 0; $i < strlen($bit_string); $i++){
            $bits[]= (int)$bit_string[$i];
        }
        return $bits;
    }

}