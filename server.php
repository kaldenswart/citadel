<?php

namespace Citadel;

use Citadel\server\DNS;
use Citadel\server\Resolvers\UpstreamResolver;

include_once(__DIR__ . "/vendor/autoload.php");

$upstream_resolver = new UpstreamResolver("1.1.1.1");
$dns = new DNS($upstream_resolver);

$dns->setErrorCallback(function(\Exception $e){
    echo $e->getMessage();
});

try{
    $dns->start();
}catch (\Exception $e){
    echo $e->getMessage();
}