<?php

namespace AllSeeingEye\server\Resolvers;

use AllSeeingEye\server\DNS;
use AllSeeingEye\server\Packet;
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