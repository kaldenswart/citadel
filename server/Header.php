<?php

namespace Citadel\server;

final class Header {

    private $id;
    private $query_or_response;
    private $operation_code;
    private $authoritative_answer;
    private $truncated_message;
    private $recursion_desired;
    private $recursion_available;
    private $z;
    private $response_code;
    private $question_count;
    private $answer_count;
    private $authority_count;
    private $additional_count;

    public function __construct($bits = null) {
        if($bits !== null) {
            $this->extractFromBits(...$bits);
        }
    }

    private function extractFromBits(int... $bits){
        $index = 0;
        $id = Util::array_extract($bits, $index, ($index += 16));
        $query_or_response = Util::array_extract($bits, $index, ($index += 1));
        $operation_code = Util::array_extract($bits, $index, ($index += 4));
        $authoritative_answer = Util::array_extract($bits, $index, ($index += 1));
        $truncated_message = Util::array_extract($bits, $index, ($index += 1));
        $recursion_desired = Util::array_extract($bits, $index, ($index += 1));
        $recursion_available = Util::array_extract($bits, $index, ($index += 1));
        $z = Util::array_extract($bits, $index, ($index += 3));
        $response_code = Util::array_extract($bits, $index, ($index += 4));
        $question_count = Util::array_extract($bits, $index, ($index += 16));
        $answer_count = Util::array_extract($bits, $index, ($index += 16));
        $authority_count = Util::array_extract($bits, $index, ($index += 16));
        $additional_count = Util::array_extract($bits, $index, ($index + 16));

        $this->id = $id;
        $this->query_or_response = Util::bits2int(...$query_or_response);
        $this->operation_code = $operation_code;
        $this->authoritative_answer = Util::bits2int(...$authoritative_answer);
        $this->truncated_message = Util::bits2int(...$truncated_message);
        $this->recursion_desired = Util::bits2int(...$recursion_desired);
        $this->recursion_available = Util::bits2int(...$recursion_available);
        $this->z = $z;
        $this->response_code = $response_code;
        $this->question_count = Util::bits2int(...$question_count);
        $this->answer_count = Util::bits2int(...$answer_count);
        $this->authority_count = Util::bits2int(...$authority_count);
        $this->additional_count = Util::bits2int(...$additional_count);
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getQueryOrResponse() {
        return $this->query_or_response;
    }

    /**
     * @param mixed $query_or_response
     */
    public function setQueryOrResponse($query_or_response): void {
        $this->query_or_response = $query_or_response;
    }

    /**
     * @return mixed
     */
    public function getOperationCode() {
        return $this->operation_code;
    }

    /**
     * @param mixed $operation_code
     */
    public function setOperationCode($operation_code): void {
        $this->operation_code = $operation_code;
    }

    /**
     * @return mixed
     */
    public function getAuthoritativeAnswer() {
        return $this->authoritative_answer;
    }

    /**
     * @param mixed $authoritative_answer
     */
    public function setAuthoritativeAnswer($authoritative_answer): void {
        $this->authoritative_answer = $authoritative_answer;
    }

    /**
     * @return mixed
     */
    public function getTruncatedMessage() {
        return $this->truncated_message;
    }

    /**
     * @param mixed $truncated_message
     */
    public function setTruncatedMessage($truncated_message): void {
        $this->truncated_message = $truncated_message;
    }

    /**
     * @return mixed
     */
    public function getRecursionDesired() {
        return $this->recursion_desired;
    }

    /**
     * @param mixed $recursion_desired
     */
    public function setRecursionDesired($recursion_desired): void {
        $this->recursion_desired = $recursion_desired;
    }

    /**
     * @return mixed
     */
    public function getRecursionAvailable() {
        return $this->recursion_available;
    }

    /**
     * @param mixed $recursion_available
     */
    public function setRecursionAvailable($recursion_available): void {
        $this->recursion_available = $recursion_available;
    }

    /**
     * @return mixed
     */
    public function getZ() {
        return $this->z;
    }

    /**
     * @param mixed $z
     */
    public function setZ($z): void {
        $this->z = $z;
    }

    /**
     * @return mixed
     */
    public function getResponseCode() {
        return $this->response_code;
    }

    /**
     * @param mixed $response_code
     */
    public function setResponseCode($response_code): void {
        $this->response_code = $response_code;
    }

    /**
     * @return mixed
     */
    public function getQuestionCount() {
        return $this->question_count;
    }

    /**
     * @param mixed $question_count
     */
    public function setQuestionCount($question_count): void {
        $this->question_count = $question_count;
    }

    /**
     * @return mixed
     */
    public function getAnswerCount() {
        return $this->answer_count;
    }

    /**
     * @param mixed $answer_count
     */
    public function setAnswerCount($answer_count): void {
        $this->answer_count = $answer_count;
    }

    /**
     * @return mixed
     */
    public function getAuthorityCount() {
        return $this->authority_count;
    }

    /**
     * @param mixed $authority_count
     */
    public function setAuthorityCount($authority_count): void {
        $this->authority_count = $authority_count;
    }

    /**
     * @return mixed
     */
    public function getAdditionalCount() {
        return $this->additional_count;
    }

    /**
     * @param mixed $additional_count
     */
    public function setAdditionalCount($additional_count): void {
        $this->additional_count = $additional_count;
    }

    /**
     * @return int[]
     */
    public function toBits() : array{
        $bits = [];

        foreach($this->id as $bit){
            $bits []= $bit;
        }

        $bits []= (int)($this->query_or_response);

        foreach($this->operation_code as $bit){
            $bits []= $bit;
        }

        $bits []= (int)($this->authoritative_answer);
        $bits []= (int)($this->truncated_message);
        $bits []= (int)($this->recursion_desired);
        $bits []= (int)($this->recursion_available);

        foreach($this->z as $bit){
            $bits []= $bit;
        }

        foreach($this->response_code as $bit){
            $bits []= $bit;
        }

        $qdcount = Util::int2bits($this->question_count, 16);
        foreach($qdcount as $bit){
            $bits []= $bit;
        }

        $ancount = Util::int2bits($this->answer_count, 16);
        foreach($ancount as $bit){
            $bits []= $bit;
        }

        $nscount = Util::int2bits($this->authority_count, 16);
        foreach($nscount as $bit){
            $bits []= $bit;
        }

        $arcount = Util::int2bits($this->additional_count, 16);
        foreach($arcount as $bit){
            $bits []= $bit;
        }

        return $bits;
    }

}