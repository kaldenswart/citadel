<?php

namespace Citadel\tests\server;

use Citadel\server\Enums\RecordClass;
use Citadel\server\Enums\RecordType;
use Citadel\server\Record;
use Citadel\server\Util;
use PHPUnit\Framework\TestCase;

class RecordTest extends TestCase {

    public function testRecordAccessor(){
        $bits = [
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, //ID
            0, //0:Query Or 1:Response
            0, 0, 0, 0, //Operation Code
            0, //Authoritative Answer
            0, //Truncated Message
            0, //Recursion Desired
            0, //Recursion Available
            0, 0, 0, //Z
            0, 0, 0, 0, //Response Code
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, //Question Count
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, //Answer Count
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, //Authority Count
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, //Additional Count

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
            1, 0, 1, 0, 1, 0, 1, 0
        ];

        $bit_position = 96;
        $question = Record::extractFromBits($bit_position, false, ...$bits);

        $this->assertEquals("google.com", $question->getName());
        $this->assertEquals(12, $question->getNameBytePosition());
        $this->assertEquals(RecordType::CNAME, $question->getType());
        $this->assertEquals(RecordClass::CH, $question->getClass());

        $question_output_bits = $question->toBits();
        $this->assertEquals(Util::array_extract($bits, 96, 224), $question_output_bits);

        $answer = Record::extractFromBits($bit_position, true, ...$bits);

        $this->assertEquals("google.com", $answer->getName());
        $this->assertEquals(12, $answer->getNameBytePosition());
        $this->assertEquals(RecordType::MD, $answer->getType());
        $this->assertEquals(RecordClass::CS, $answer->getClass());
        $this->assertEquals(475, $answer->getTtl());
        $this->assertEquals(1, $answer->getLength());
        $this->assertEquals([1, 0, 1, 0, 1, 0, 1, 0], $answer->getData());

        $answer_output_bits = $answer->toBits(12);
        $this->assertEquals(Util::array_extract($bits, 224), $answer_output_bits);
    }

    public function testRecordMutator(){
        $record = new Record();

        $record->setName("google.com");
        $record->setNameBytePosition(12);
        $record->setType(RecordType::MX);
        $record->setClass(RecordClass::IN);
        $record->setTtl(119);
        $record->setLength(4);
        $record->setData([1, 1, 0, 0, 0, 0, 0, 0, 1, 0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 1]);

        $this->assertEquals(12, $record->getNameBytePosition());

        $output_bits = $record->toBits();
        $expected_bits = [
            0, 0, 0, 0, 0, 1, 1, 0/*6*/, 0, 1, 1, 0, 0, 1, 1, 1/*g*/, 0, 1, 1, 0, 1, 1, 1, 1/*o*/, 0, 1, 1, 0, 1, 1, 1, 1/*o*/, 0, 1, 1, 0, 0, 1, 1, 1/*g*/, 0, 1, 1, 0, 1, 1, 0, 0/*l*/, 0, 1, 1, 0, 0, 1, 0, 1/*e*/, 0, 0, 0, 0, 0, 0, 1, 1/*3*/, 0, 1, 1, 0, 0, 0, 1, 1/*c*/, 0, 1, 1, 0, 1, 1, 1, 1/*o*/, 0, 1, 1, 0, 1, 1, 0, 1/*m*/, 0, 0, 0, 0, 0, 0, 0, 0/*0*/,
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, //Type
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, //Class
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 0, 1, 1, 1, //TTL
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, //Length
            1, 1, 0, 0, 0, 0, 0, 0, 1, 0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 1 //Data
        ];

        $this->assertEquals($expected_bits, $output_bits);
    }

}