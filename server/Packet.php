<?php

namespace AllSeeingEye\server;

final class Packet {

    private $header;

    public function __construct(int... $bytes) {
        $bits = Util::bytes2bits(...$bytes);

        $this->header = new Header(...Util::array_extract($bits, 0, 96));
    }

    /**
     * @return Header
     */
    public function getHeader(): Header {
        return $this->header;
    }

}