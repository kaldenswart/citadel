<?php

namespace AllSeeingEye\server;

final class Packet {

    private $header;
    private $questions;

    public function __construct(int... $bytes) {
        $bits = Util::bytes2bits(...$bytes);

        $this->header = new Header(...Util::array_extract($bits, 0, 96));
        $this->questions = Record::extractFromBits($this->header, 0, ...$bits);
    }

    /**
     * @return Header
     */
    public function getHeader(): Header {
        return $this->header;
    }

    /**
     * @return int[]
     */
    public function toBits() : array{
        $bits = $this->header->toBits();

        foreach($this->questions as $question){
            $question_bits = $question->toBits();
            foreach($question_bits as $bit){
                $bits []= $bit;
            }
        }

        return $bits;
    }

}