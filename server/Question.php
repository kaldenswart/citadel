<?php

namespace AllSeeingEye\server;

class Question {

    private $name;
    private $type;
    private $class;

    public function __construct(string $name, int $type, int $class) {
        $this->name = $name;
        $this->type = $type;
        $this->class = $class;
    }

    /**
     * @param Header $header
     * @param int ...$bits
     * @return Question[]
     */
    public static function extractFromBits(Header $header, int... $bits){
        $questions = [];

        $bit_position = 0;
        $body_bits = Util::array_extract($bits, 96, sizeof($bits));

        for($i = 0; $i < $header->getQuestionCount(); $i++){
            $msb = Util::array_extract($body_bits, $bit_position, ($bit_position + 2));
            $name = "";
            if($msb === [1, 1]){ //Jump Position
                //@todo Add in jump position reading
            }else{ //Normal Parse
                $name = Question::extractName($bit_position, ...$body_bits);
            }

            $type = Util::bits2int(...Util::array_extract($body_bits, $bit_position, $bit_position += 16));
            $class = Util::bits2int(...Util::array_extract($body_bits, $bit_position, $bit_position += 16));

            $questions []= new Question($name, $type, $class);
        }

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

}