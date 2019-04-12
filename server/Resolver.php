<?php

namespace Citadel\server;

interface Resolver {

    public function resolve(DNS $dns, Packet $packet);

}