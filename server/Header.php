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
            $this->id = Util::array_extract($bits, $index = 0, ($index += 16));
            $this->query_or_response = Util::bits2int(...Util::array_extract($bits, $index, ($index += 1)));
            $this->operation_code = Util::array_extract($bits, $index, ($index += 4));
            $this->authoritative_answer = Util::bits2int(...Util::array_extract($bits, $index, ($index += 1)));
            $this->truncated_message = Util::bits2int(...Util::array_extract($bits, $index, ($index += 1)));
            $this->recursion_desired = Util::bits2int(...Util::array_extract($bits, $index, ($index += 1)));
            $this->recursion_available = Util::bits2int(...Util::array_extract($bits, $index, ($index += 1)));
            $this->z = Util::array_extract($bits, $index, ($index += 3));
            $this->response_code = Util::array_extract($bits, $index, ($index += 4));
            $this->question_count = Util::bits2int(...Util::array_extract($bits, $index, ($index += 16)));
            $this->answer_count = Util::bits2int(...Util::array_extract($bits, $index, ($index += 16)));
            $this->authority_count = Util::bits2int(...Util::array_extract($bits, $index, ($index += 16)));
            $this->additional_count = Util::bits2int(...Util::array_extract($bits, $index, ($index + 16)));
        }
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

        array_push($bits, ...$this->id);

        $bits []= (int)($this->query_or_response);

        array_push($bits, ...$this->operation_code);

        $bits []= (int)($this->authoritative_answer);
        $bits []= (int)($this->truncated_message);
        $bits []= (int)($this->recursion_desired);
        $bits []= (int)($this->recursion_available);

        array_push($bits, ...$this->z);
        array_push($bits, ...$this->response_code);

        array_push($bits, ...Util::int2bits($this->question_count, 16));
        array_push($bits, ...Util::int2bits($this->answer_count, 16));
        array_push($bits, ...Util::int2bits($this->authority_count, 16));
        array_push($bits, ...Util::int2bits($this->additional_count, 16));

        return $bits;
    }

}