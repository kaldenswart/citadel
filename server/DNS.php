<?php

namespace AllSeeingEye\server;

class DNS {

    private $socket;

    public function __construct() {

    }

    public function start(){
        echo "Starting the all seeing eye\n";

        if(!($this->socket = socket_create(AF_INET, SOCK_DGRAM, 0))){
            $errorcode = socket_last_error();
            $errormessage = socket_strerror($errorcode);

            die("Failed to start: [$errorcode] $errormessage\n");
        }

        if(!socket_bind($this->socket, "0.0.0.0", 53)){
            $errorcode = socket_last_error();
            $errormessage = socket_strerror($errorcode);

            die("Failed to bind: [$errorcode] $errormessage\n");
        }

        echo "The all seeing eye bas started\n";

        while(true){
            $data = socket_recvfrom($this->socket, $buffer, 512, 0, $remote_ip, $remote_port);
            print_r($buffer);
            print_r($remote_ip);
            print_r($remote_port);
        }
    }

}