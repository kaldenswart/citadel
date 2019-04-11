<?php

namespace AllSeeingEye;

use AllSeeingEye\server\DNS;
use AllSeeingEye\server\Resolvers\UpstreamResolver;

include_once(__DIR__ . "/vendor/autoload.php");

$upstream_resolver = new UpstreamResolver("8.8.8.8");
$dns = new DNS($upstream_resolver);
$dns->start();