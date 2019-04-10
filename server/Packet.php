<?php

namespace AllSeeingEye\server;

final class Packet {

    private $header;
    private $questions;

    public function __construct(int... $bytes) {
        $bits = Util::bytes2bits(...$bytes);

        $this->header = new Header(...Util::array_extract($bits, 0, 96));
        $this->questions = Question::extractFromBits($this->header, ...$bits);

        foreach($this->questions as $question){
            echo "Name: " . $question->getName() . "\n";
            echo "Type: " . $question->getType() . "\n";
            echo "Class: " . $question->getClass() . "\n";
            echo "\n";
        }
    }

    /**
     * @return Header
     */
    public function getHeader(): Header {
        return $this->header;
    }

}