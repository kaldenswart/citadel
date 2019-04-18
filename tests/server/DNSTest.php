<?php

namespace Citadel\tests\server;

use Citadel\server\DNS;
use Citadel\server\Resolvers\UpstreamResolver;
use PHPUnit\Framework\TestCase;

class DNSTest extends TestCase {

    public function testDNS(){
        $dns = new DNS(new UpstreamResolver("1.1.1.1"));

        $this->assertEquals("0.0.0.0", $dns->getIp());
        $this->assertEquals(53, $dns->getPort());

        $dns->setIp("192.168.1.1");
        $dns->setPort(92);
        $dns->setErrorCallback(function(){
            //nothing
        });

        $this->assertEquals("192.168.1.1", $dns->getIp());
        $this->assertEquals(92, $dns->getPort());
    }

}