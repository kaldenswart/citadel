<?php

namespace Citadel\server\Resolvers;

use Citadel\server\DNS;
use Citadel\server\Packet;
use Citadel\server\Resolver;
use Citadel\server\Util;

class UpstreamResolver implements Resolver {

    private $dns;

    private $expected_responses = [];

    public function __construct(string $dns) {
        $this->dns = $dns;
    }

    /**
     * @param DNS $dns
     * @param Packet $packet
     * @codeCoverageIgnore
     */
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