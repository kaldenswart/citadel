<?php

namespace Citadel\tests\server;

use Citadel\server\Header;
use PHPUnit\Framework\TestCase;

class HeaderTest extends TestCase {

    public function testHeaderAccessor(){
        $bits = [
            0, 1, 1, 1, 0, 1, 0, 0, 0, 1, 1, 0, 1, 0, 1, 1, //ID
            0, //0:Query Or 1:Response
            0, 1, 0, 0, //Operation Code
            1, //Authoritative Answer
            0, //Truncated Message
            1, //Recursion Desired
            1, //Recursion Available
            0, 0, 0, //Z
            0, 1, 0, 0, //Response Code
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, //Question Count
            0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, //Answer Count
            0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, //Authority Count
            0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0 //Additional Count
        ];

        $header = new Header($bits);

        $this->assertEquals([0, 1, 1, 1, 0, 1, 0, 0, 0, 1, 1, 0, 1, 0, 1, 1], $header->getId());
        $this->assertEquals(0, $header->getQueryOrResponse());
        $this->assertEquals([0, 1, 0, 0], $header->getOperationCode());
        $this->assertEquals(1, $header->getAuthoritativeAnswer());
        $this->assertEquals(0, $header->getTruncatedMessage());
        $this->assertEquals(1, $header->getRecursionDesired());
        $this->assertEquals(1, $header->getRecursionAvailable());
        $this->assertEquals([0, 0, 0], $header->getZ());
        $this->assertEquals([0, 1, 0, 0], $header->getResponseCode());
        $this->assertEquals(16, $header->getQuestionCount());
        $this->assertEquals(128, $header->getAnswerCount());
        $this->assertEquals(1024, $header->getAuthorityCount());
        $this->assertEquals(8192, $header->getAdditionalCount());

        $output_bits = $header->toBits();
        $this->assertEquals($bits, $output_bits);
    }

    public function testHeaderMutator(){
        $header = new Header();

        $header->setId([1, 0, 0, 0, 1, 0, 1, 1, 1, 0, 0, 1, 0, 1, 0, 0]);
        $header->setQueryOrResponse(1);
        $header->setOperationCode([1, 0, 1, 1]);
        $header->setAuthoritativeAnswer(0);
        $header->setTruncatedMessage(1);
        $header->setRecursionDesired(0);
        $header->setRecursionAvailable(0);
        $header->setZ([1, 1, 1]);
        $header->setResponseCode([1, 0, 1, 1]);
        $header->setQuestionCount(73);
        $header->setAnswerCount(33667);
        $header->setAuthorityCount(20490);
        $header->setAdditionalCount(20874);

        $output_bits = $header->toBits();

        $expected_bits = [
            1, 0, 0, 0, 1, 0, 1, 1, 1, 0, 0, 1, 0, 1, 0, 0, //ID
            1, //0:Query Or 1:Response
            1, 0, 1, 1, //Operation Code
            0, //Authoritative Answer
            1, //Truncated Message
            0, //Recursion Desired
            0, //Recursion Available
            1, 1, 1, //Z
            1, 0, 1, 1, //Response Code
            0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 1, 0, 0, 1, //Question Count
            1, 0, 0, 0, 0, 0, 1, 1, 1, 0, 0, 0, 0, 0, 1, 1, //Answer Count
            0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 1, 0, //Authority Count
            0, 1, 0, 1, 0, 0, 0, 1, 1, 0, 0, 0, 1, 0, 1, 0 //Additional Count
        ];

        $this->assertEquals($expected_bits, $output_bits);
    }

}