<?php

namespace AllSeeingEye\server;

interface Resolver {

    public function resolve(Packet $packet) : Packet;

}