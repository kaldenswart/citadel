<?php

namespace AllSeeingEye\server\Resolvers;

use AllSeeingEye\server\Packet;
use AllSeeingEye\server\Resolver;

class UpstreamResolver implements Resolver {

    private $primary_dns;
    private $secondary_dns;

    public function __construct(string $primary_dns, string $secondary_dns) {
        $this->primary_dns = $primary_dns;
        $this->secondary_dns = $secondary_dns;
    }

    public function resolve(Packet $packet): Packet {
        $header = $packet->getHeader();
        $header->setQueryOrResponse(true);
        $packet->setHeader($header);

        return $packet;
    }

}