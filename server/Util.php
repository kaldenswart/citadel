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

}