<?php

namespace Citadel\server;

class Record {

    private $name;
    private $name_byte_position;
    private $type;
    private $class;
    private $ttl;
    private $length;
    private $data;

    public function __construct(string $name = null, int $name_byte_position = null, int $type = null, int $class = null) {
        $this->name = $name;
        $this->name_byte_position = $name_byte_position;
        $this->type = $type;
        $this->class = $class;
    }

    /**
     * @param int $bit_position
     * @param bool $full_preamble
     * @param int[] $bits
     * @return static
     */
    public static function extractFromBits(int &$bit_position, bool $full_preamble = false, int... $bits){
        $name_byte_location = 0;
        $name = static::extractNameFromBitArray($bit_position, $name_byte_location, ...$bits);

        $type = Util::bits2int(...Util::array_extract($bits, $bit_position, $bit_position += 16));
        $class = Util::bits2int(...Util::array_extract($bits, $bit_position, $bit_position += 16));

        $ttl = ($full_preamble) ? Util::bits2int(...Util::array_extract($bits, $bit_position, $bit_position += 32)) : null;
        $length = ($full_preamble) ? Util::bits2int(...Util::array_extract($bits, $bit_position, $bit_position += 16)) : null;
        $data = ($full_preamble & $length > 0) ? Util::array_extract($bits, $bit_position, ($bit_position += ($length * 8))) : null;

        $record = new static($name, $name_byte_location, $type, $class);
        $record->setTtl($ttl);
        $record->setLength($length);
        $record->setData($data);
        return $record;
    }

    private static function extractNameFromBitArray(int &$bit_position, int &$name_byte_location, int... $bits) : string{
        $name = "";
        $reading = true;
        $name_byte_location = $bit_position / 8;
        while($reading){
            $byte = Util::bits2int(...Util::array_extract($bits, $bit_position, ($bit_position += 8)));

            switch($byte){
                case 0:
                    $name = substr($name, 0, strlen($name)-1);
                    $reading = false;
                    break;
                case 192:
                    $position = Util::bits2int(...Util::array_extract($bits, $bit_position, ($bit_position += 8))) * 8;
                    $return_position = $bit_position;
                    $bit_position = $position;
                    $name .= static::extractNameFromBitArray($bit_position, $name_byte_location, ...$bits);
                    $name_byte_location = $position / 8;
                    $bit_position = $return_position;
                    $reading = false;
                    break;
                default:
                    $name .= self::readName($bit_position, $byte, ...$bits);
                    break;
            }
        }
        return $name;
    }

    private static function readName(int &$bit_position, int $byte, int... $bits){
        $name = "";
        for($x = 0; $x < $byte; $x++){
            $name_char = Util::bits2int(...Util::array_extract($bits, $bit_position, $bit_position += 8));
            $name .= chr($name_char);
        }
        $name .= ".";
        return $name;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getNameBytePosition(): int {
        return $this->name_byte_position;
    }

    /**
     * @param int $name_byte_position
     */
    public function setNameBytePosition(int $name_byte_position): void {
        $this->name_byte_position = $name_byte_position;
    }

    /**
     * @return int
     */
    public function getType(): int {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType(int $type): void {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getClass(): int {
        return $this->class;
    }

    /**
     * @param int $class
     */
    public function setClass(int $class): void {
        $this->class = $class;
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
    public function setTtl(int $ttl = null): void {
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
    public function setLength(int $length = null): void {
        $this->length = $length;
    }

    /**
     * @return array
     */
    public function getData(): array {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data = null): void {
        $this->data = $data;
    }

    /**
     * @param $name_bit_location
     * @return int[]
     */
    public function toBits($name_bit_location = false) : array{
        $bits = [];
        if($name_bit_location === false){
            Util::array_push($bits, ...$this->convertNameToBitArray());
        }else{
            Util::array_push($bits, ...[1, 1, 0, 0, 0, 0, 0, 0]);
            Util::array_push($bits, ...Util::int2bits($name_bit_location, 8));
        }
        Util::array_push($bits, ...Util::int2bits($this->type, 16));
        Util::array_push($bits, ...Util::int2bits($this->class, 16));

        if($this->ttl !== null) {
            Util::array_push($bits, ...Util::int2bits($this->ttl, 32));
        }
        if($this->length !== null){
            Util::array_push($bits, ...Util::int2bits($this->length, 16));
        }
        if($this->data !== null){
            Util::array_push($bits, ...$this->data);
        }

        return $bits;
    }

    private function convertNameToBitArray(){
        $bits = [];

        $name_split = explode(".", $this->name);
        foreach($name_split as $name){
            $name_length = strlen($name);
            Util::array_push($bits, ...Util::int2bits($name_length, 8));

            for($i = 0; $i < $name_length; $i++){
                Util::array_push($bits, ...Util::int2bits(ord($name[$i]), 8));
            }
        }
        Util::array_push($bits, ...Util::int2bits(0, 8));
        return $bits;
    }

}