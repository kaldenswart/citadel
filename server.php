<?php

namespace AllSeeingEye;

use AllSeeingEye\server\DNS;

include_once(__DIR__ . "/vendor/autoload.php");

$dns = new DNS();
$dns->start();