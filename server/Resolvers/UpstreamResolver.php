<?php

namespace AllSeeingEye\server\Resolvers;

use AllSeeingEye\server\DNS;
use AllSeeingEye\server\Enums\RecordType;
use AllSeeingEye\server\Packet;
use AllSeeingEye\server\Record;
use AllSeeingEye\server\Resolver;
use AllSeeingEye\server\Util;

class UpstreamResolver implements Resolver {

    private $dns;

    private $expected_responses = [];

    public function __construct(string $dns) {
        $this->dns = $dns;
    }

    public function resolve(DNS $dns, Packet $packet) {
        $id = Util::bits2int(...$packet->getHeader()->getId());
        if($packet->isQuery()){
//            if($packet->getHeader()->getQuestionCount() == 1){
//                $question = $packet->getQuestions()[0];
//                if($question->getName() == "google.com" && $question->getType() == RecordType::A){
//                    $return_ip = array_merge(Util::int2bits(192, 8), Util::int2bits(168, 8), Util::int2bits(1, 8), Util::int2bits(18, 8));
//                    $answer = new Record("google.com", $question->getNameBytePosition(), RecordType::A, $question->getClass(), 5, 4, $return_ip);
//                    $packet->setAnswers([$answer]);
//
//                    $header = $packet->getHeader();
//                    $header->setQueryOrResponse(1);
//                    $header->setAnswerCount(1);
//                    $packet->setHeader($header);
//
//                    $dns->sendPacket($packet);
//                    return;
//                }
//            }

            $this->expected_responses [$id]= [
                "id" => $id,
                "remote_ip" => $packet->getRemoteIp(),
                "remote_port" => $packet->getRemotePort()
            ];

            $packet->setRemoteIp($this->dns);
            $packet->setRemotePort(53);

            $dns->sendPacket($packet);
        }else{
            if(isset($this->expected_responses[$id])) {
                $expected_response = $this->expected_responses[$id];

                $packet->setRemoteIp($expected_response["remote_ip"]);
                $packet->setRemotePort($expected_response["remote_port"]);

                $dns->sendPacket($packet);
            }
        }
    }

}