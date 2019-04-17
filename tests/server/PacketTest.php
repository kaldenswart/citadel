<?php

namespace Citadel\tests\server;

use Citadel\server\Enums\RecordClass;
use Citadel\server\Packet;
use Citadel\server\Util;
use PHPUnit\Framework\TestCase;

class PacketTest extends TestCase {

    public function testPacket(){
        $bits = [
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, //ID
            1, //0:Query Or 1:Response
            0, 0, 0, 0, //Operation Code
            0, //Authoritative Answer
            0, //Truncated Message
            0, //Recursion Desired
            0, //Recursion Available
            0, 0, 0, //Z
            0, 0, 0, 0, //Response Code
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, //Question Count
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, //Answer Count
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, //Authority Count
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, //Additional Count

            //Question
            0, 0, 0, 0, 0, 1, 1, 0/*6*/, 0, 1, 1, 0, 0, 1, 1, 1/*g*/, 0, 1, 1, 0, 1, 1, 1, 1/*o*/, 0, 1, 1, 0, 1, 1, 1, 1/*o*/, 0, 1, 1, 0, 0, 1, 1, 1/*g*/, 0, 1, 1, 0, 1, 1, 0, 0/*l*/, 0, 1, 1, 0, 0, 1, 0, 1/*e*/, 0, 0, 0, 0, 0, 0, 1, 1/*3*/, 0, 1, 1, 0, 0, 0, 1, 1/*c*/, 0, 1, 1, 0, 1, 1, 1, 1/*o*/, 0, 1, 1, 0, 1, 1, 0, 1/*m*/, 0, 0, 0, 0, 0, 0, 0, 0/*0*/,
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 1, //Type
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, //Class

            //Answer
            1, 1, 0, 0, 0, 0, 0, 0/*192*/, 0, 0, 0, 0, 1, 1, 0, 0,/*12*/
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, //Type
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, //Class
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 0, 1, 1, 0, 1, 1, //TTL
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, //Length
            1, 0, 1, 0, 1, 0, 1, 0,

            //Authority
            1, 1, 0, 0, 0, 0, 0, 0/*192*/, 0, 0, 0, 0, 1, 1, 0, 0,/*12*/
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, //Type
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, //Class
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 0, 1, 1, 0, 1, 1, //TTL
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, //Length
            1, 0, 1, 0, 1, 0, 1, 0,

            //Additional
            1, 1, 0, 0, 0, 0, 0, 0/*192*/, 0, 0, 0, 0, 1, 1, 0, 0,/*12*/
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, //Type
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, //Class
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 0, 1, 1, 0, 1, 1, //TTL
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, //Length
            1, 0, 1, 0, 1, 0, 1, 0
        ];

        $bytes = Util::bits2bytes(...$bits);

        $packet = new Packet("192.168.1.1", 53, ...$bytes);

        $this->assertFalse($packet->isQuery());

        $this->assertEquals("192.168.1.1", $packet->getRemoteIp());
        $this->assertEquals(53, $packet->getRemotePort());

        $packet->setRemoteIp("10.0.0.1");
        $packet->setRemotePort(84);
        $this->assertEquals("10.0.0.1", $packet->getRemoteIp());
        $this->assertEquals(84, $packet->getRemotePort());

        $header = $packet->getHeader();
        $header_bits = $header->toBits();
        $this->assertEquals(Util::array_extract($bits, 0, 96), $header_bits);

        $question = $packet->getQuestions()[0];
        $question_bits = $question->toBits();
        $this->assertEquals(Util::array_extract($bits, 96, 224), $question_bits);

        $answer = $packet->getAnswers()[0];
        $answer_bits = $answer->toBits($question->getNameBytePosition());
        $this->assertEquals(Util::array_extract($bits, 224, 328), $answer_bits);

        $authority = $packet->getAuthorities()[0];
        $authority_bits = $answer->toBits($authority->getNameBytePosition());
        $this->assertEquals(Util::array_extract($bits, 328, 432), $authority_bits);

        $additional = $packet->getAdditionals()[0];
        $additional_bits = $answer->toBits($additional->getNameBytePosition());
        $this->assertEquals(Util::array_extract($bits, 432, 536), $additional_bits);

        $header->setId([1, 0, 1, 0, 1, 0, 1, 0, 1, 0, 1, 0, 1, 0, 1, 0]);
        $packet->setHeader($header);

        $question->setClass(RecordClass::CS);
        $packet->setQuestions(...[$question]);

        $answer->setClass(RecordClass::IN);
        $packet->setAnswers(...[$answer]);

        $authority->setClass(RecordClass::CH);
        $packet->setAuthorities(...[$authority]);

        $additional->setClass(RecordClass::HS);
        $packet->setAdditionals(...[$additional]);

        $output_bits = $packet->toBits();
        $expected_bits = [
            1, 0, 1, 0, 1, 0, 1, 0, 1, 0, 1, 0, 1, 0, 1, 0, //ID
            1, //0:Query Or 1:Response
            0, 0, 0, 0, //Operation Code
            0, //Authoritative Answer
            0, //Truncated Message
            0, //Recursion Desired
            0, //Recursion Available
            0, 0, 0, //Z
            0, 0, 0, 0, //Response Code
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, //Question Count
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, //Answer Count
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, //Authority Count
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, //Additional Count

            //Question
            0, 0, 0, 0, 0, 1, 1, 0/*6*/, 0, 1, 1, 0, 0, 1, 1, 1/*g*/, 0, 1, 1, 0, 1, 1, 1, 1/*o*/, 0, 1, 1, 0, 1, 1, 1, 1/*o*/, 0, 1, 1, 0, 0, 1, 1, 1/*g*/, 0, 1, 1, 0, 1, 1, 0, 0/*l*/, 0, 1, 1, 0, 0, 1, 0, 1/*e*/, 0, 0, 0, 0, 0, 0, 1, 1/*3*/, 0, 1, 1, 0, 0, 0, 1, 1/*c*/, 0, 1, 1, 0, 1, 1, 1, 1/*o*/, 0, 1, 1, 0, 1, 1, 0, 1/*m*/, 0, 0, 0, 0, 0, 0, 0, 0/*0*/,
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 1, //Type
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, //Class

            //Answer
            1, 1, 0, 0, 0, 0, 0, 0/*192*/, 0, 0, 0, 0, 1, 1, 0, 0,/*12*/
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, //Type
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, //Class
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 0, 1, 1, 0, 1, 1, //TTL
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, //Length
            1, 0, 1, 0, 1, 0, 1, 0,

            //Authority
            1, 1, 0, 0, 0, 0, 0, 0/*192*/, 0, 0, 0, 0, 1, 1, 0, 0,/*12*/
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, //Type
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, //Class
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 0, 1, 1, 0, 1, 1, //TTL
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, //Length
            1, 0, 1, 0, 1, 0, 1, 0,

            //Additional
            1, 1, 0, 0, 0, 0, 0, 0/*192*/, 0, 0, 0, 0, 1, 1, 0, 0,/*12*/
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, //Type
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, //Class
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 0, 1, 1, 0, 1, 1, //TTL
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, //Length
            1, 0, 1, 0, 1, 0, 1, 0
        ];

        $this->assertEquals($expected_bits, $output_bits);
    }

}