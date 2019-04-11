<?php

namespace AllSeeingEye\server;

final class Packet {

    private $header;
    private $questions;

    private $remote_ip;
    private $remote_port;

    public function __construct(string $remote_ip, string $remote_port, int... $bytes) {
        $this->remote_ip = $remote_ip;
        $this->remote_port = $remote_port;

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

        foreach($this->questions as $question){
            $question_bits = $question->toBits();
            foreach($question_bits as $bit){
                $bits []= $bit;
            }
        }

        //@todo Add other 3 types

        return $bits;
    }

}