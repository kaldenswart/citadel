<?php

namespace AllSeeingEye\server;

class Record {

    private $name;
    private $type;
    private $class;
    private $ttl;
    private $length;

    public function __construct(string $name, int $type, int $class, int $ttl = null, int $length = null) {
        $this->name = $name;
        $this->type = $type;
        $this->class = $class;
        $this->ttl = $ttl;
        $this->length = $length;
    }

    /**
     * @param Header $header
     * @param int $starting_position
     * @param int ...$bits
     * @return static[]
     */
    public static function extractFromBits(Header $header, int $starting_position, int... $bits){
        $questions = [];

        $bit_position = $starting_position;
        $body_bits = Util::array_extract($bits, 96, sizeof($bits));

        for($i = 0; $i < $header->getQuestionCount(); $i++){
            $msb = Util::array_extract($body_bits, $bit_position, ($bit_position + 2));
            $name = "";
            if($msb === [1, 1]){ //Jump Position
                //@todo Add in jump position reading
            }else{ //Normal Parse
                $name = static::extractName($bit_position, ...$body_bits);
            }

            $type = Util::bits2int(...Util::array_extract($body_bits, $bit_position, $bit_position += 16));
            $class = Util::bits2int(...Util::array_extract($body_bits, $bit_position, $bit_position += 16));

            $questions []= new static($name, $type, $class);
        }

        //@todo Add parsing for 3 other record sections

        return $questions;
    }

    private static function extractName(int &$bit_position, int... $body_bits) : string{
        $name = "";
        $reading_name = true;
        while($reading_name){
            $name_length = Util::bits2int(...Util::array_extract($body_bits, $bit_position, $bit_position += 8));
            if($name_length === 0){
                $reading_name = false;
            }else{
                for($x = 0; $x < $name_length; $x++){
                    $name_char = Util::bits2int(...Util::array_extract($body_bits, $bit_position, $bit_position += 8));
                    $name .= chr($name_char);
                }
                $name .= ".";
            }
        }
        $name = substr($name, 0, strlen($name)-1);
        return $name;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getType(): int {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getClass(): int {
        return $this->class;
    }

    /**
     * @return int
     */
    public function getTtl(): int {
        return $this->ttl;
    }

    /**
     * @param int $ttl
     */
    public function setTtl(int $ttl): void {
        $this->ttl = $ttl;
    }

    /**
     * @return int
     */
    public function getLength(): int {
        return $this->length;
    }

    /**
     * @param int $length
     */
    public function setLength(int $length): void {
        $this->length = $length;
    }

    /**
     * @return int[]
     */
    public function toBits() : array{
        $bits = [];

        $name_split = explode(".", $this->name);
        foreach($name_split as $name){
            $name_length = strlen($name);
            $name_length_bits = Util::int2bits($name_length, 8);
            foreach($name_length_bits as $bit){
                $bits []= $bit;
            }

            for($i = 0; $i < $name_length; $i++){
                $name_bits = Util::int2bits(ord($name[$i]), 8);
                foreach($name_bits as $bit){
                    $bits []= $bit;
                }
            }
        }
        $name_bits = Util::int2bits(0, 8);
        foreach($name_bits as $bit){
            $bits []= $bit;
        }

        $type_bits = Util::int2bits($this->type, 16);
        foreach($type_bits as $bit){
            $bits []= $bit;
        }

        $class_bits = Util::int2bits($this->class, 16);
        foreach($class_bits as $bit){
            $bits []= $bit;
        }

        if($this->ttl !== null){
            $ttl_bits = Util::int2bits($this->ttl, 32);
            foreach($ttl_bits as $bit){
                $bits []= $bit;
            }
        }

        if($this->length !== null){
            $length_bits = Util::int2bits($this->length, 16);
            foreach($length_bits as $bit){
                $bits []= $bit;
            }
        }

        return $bits;
    }

}