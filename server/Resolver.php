<?php

namespace AllSeeingEye\server;

interface Resolver {

    public function resolve(DNS $dns, Packet $packet);

}