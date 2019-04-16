<?php

namespace Citadel\server;

final class Packet {

    private $header;
    private $questions = [];
    private $answers = [];
    private $authorities = [];
    private $additionals = [];

    private $remote_ip;
    private $remote_port;

    public function __construct(string $remote_ip, string $remote_port, int... $bytes) {
        $this->remote_ip = $remote_ip;
        $this->remote_port = $remote_port;

        $bits = Util::bytes2bits(...$bytes);

        $bit_position = 0;

        $this->header = new Header(Util::array_extract($bits, $bit_position, $bit_position += 96));

        for($i = 0; $i < $this->header->getQuestionCount(); $i++){
            $this->questions []= Record::extractFromBits($bit_position, false, ...$bits);
        }

        for($i = 0; $i < $this->header->getAnswerCount(); $i++){
            $this->answers []= Record::extractFromBits($bit_position, true, ...$bits);
        }

        for($i = 0; $i < $this->header->getAuthorityCount(); $i++){
            $this->authorities []= Record::extractFromBits($bit_position, true, ...$bits);
        }

        for($i = 0; $i < $this->header->getAdditionalCount(); $i++){
            $this->additionals []= Record::extractFromBits($bit_position, true, ...$bits);
        }
    }

    /**
     * @return Header
     */
    public function getHeader(): Header {
        return $this->header;
    }

    /**
     * @param Header $header
     */
    public function setHeader(Header $header): void {
        $this->header = $header;
    }

    /**
     * @return Record[]
     */
    public function getQuestions(): array {
        return $this->questions;
    }

    /**
     * @param Record[] $questions
     */
    public function setQuestions(Record... $questions): void {
        $this->questions = $questions;
    }

    /**
     * @return array
     */
    public function getAnswers(): array {
        return $this->answers;
    }

    /**
     * @param array $answers
     */
    public function setAnswers(array $answers): void {
        $this->answers = $answers;
    }

    /**
     * @return array
     */
    public function getAuthorities(): array {
        return $this->authorities;
    }

    /**
     * @param array $authorities
     */
    public function setAuthorities(array $authorities): void {
        $this->authorities = $authorities;
    }

    /**
     * @return array
     */
    public function getAdditionals(): array {
        return $this->additionals;
    }

    /**
     * @param array $additionals
     */
    public function setAdditionals(array $additionals): void {
        $this->additionals = $additionals;
    }

    /**
     * @return string
     */
    public function getRemoteIp(): string {
        return $this->remote_ip;
    }

    /**
     * @param string $remote_ip
     */
    public function setRemoteIp(string $remote_ip): void {
        $this->remote_ip = $remote_ip;
    }

    /**
     * @return string
     */
    public function getRemotePort(): string {
        return $this->remote_port;
    }

    /**
     * @param string $remote_port
     */
    public function setRemotePort(string $remote_port): void {
        $this->remote_port = $remote_port;
    }

    /**
     * @return bool
     */
    public function isQuery() : bool {
        return ($this->header->getQueryOrResponse() == 0);
    }

    /**
     * @return int[]
     */
    public function toBits() : array{
        $bits = $this->header->toBits();

        $question_name_bit_locations = [];

        foreach($this->questions as $question){ /* @var $question Record */
            $question_name_bit_locations[$question->getName()] = $question->getNameBytePosition();
            $question_bits = $question->toBits();
            foreach($question_bits as $bit){
                $bits []= $bit;
            }
        }

        foreach($this->answers as $answer){ /* @var $answer Record */
            $name_bit_location = (isset($question_name_bit_locations[$answer->getName()])) ? $question_name_bit_locations[$answer->getName()] : false;
            $answer_bits = $answer->toBits($name_bit_location);
            foreach($answer_bits as $bit){
                $bits []= $bit;
            }
        }

        foreach($this->authorities as $authority){ /* @var $authority Record */
            $name_bit_location = (isset($question_name_bit_locations[$authority->getName()])) ? $question_name_bit_locations[$authority->getName()] : false;
            $authority_bits = $authority->toBits($name_bit_location);
            foreach($authority_bits as $bit){
                $bits []= $bit;
            }
        }

        foreach($this->additionals as $additional){ /* @var $additional Record */
            $name_bit_location = (isset($question_name_bit_locations[$additional->getName()])) ? $question_name_bit_locations[$additional->getName()] : false;
            $additional_bits = $additional->toBits($name_bit_location);
            foreach($additional_bits as $bit){
                $bits []= $bit;
            }
        }

        return $bits;
    }

}