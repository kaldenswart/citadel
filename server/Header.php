<?php

namespace AllSeeingEye\server;

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

    public function __construct(int... $bits) {
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
     * @return array
     */
    public function getId(): array {
        return $this->id;
    }

    /**
     * @return float|int
     */
    public function getQueryOrResponse() {
        return $this->query_or_response;
    }

    /**
     * @return array
     */
    public function getOperationCode(): array {
        return $this->operation_code;
    }

    /**
     * @return float|int
     */
    public function getAuthoritativeAnswer() {
        return $this->authoritative_answer;
    }

    /**
     * @return float|int
     */
    public function getTruncatedMessage() {
        return $this->truncated_message;
    }

    /**
     * @return float|int
     */
    public function getRecursionDesired() {
        return $this->recursion_desired;
    }

    /**
     * @return float|int
     */
    public function getRecursionAvailable() {
        return $this->recursion_available;
    }

    /**
     * @return array
     */
    public function getZ(): array {
        return $this->z;
    }

    /**
     * @return array
     */
    public function getResponseCode(): array {
        return $this->response_code;
    }

    /**
     * @return float|int
     */
    public function getQuestionCount() {
        return $this->question_count;
    }

    /**
     * @return float|int
     */
    public function getAnswerCount() {
        return $this->answer_count;
    }

    /**
     * @return float|int
     */
    public function getAuthorityCount() {
        return $this->authority_count;
    }

    /**
     * @return float|int
     */
    public function getAdditionalCount() {
        return $this->additional_count;
    }

}