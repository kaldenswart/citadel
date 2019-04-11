<?php

namespace AllSeeingEye\server;

class DNS {

    private $resolver;

    private $socket;

    public function __construct(Resolver $resolver) {
        $this->resolver = $resolver;
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
            if(($data = socket_recvfrom($this->socket, $buffer, 512, 0, $remote_ip, $remote_port)) !== false) {
                $bytes = unpack("C*", $buffer);

                $packet = new Packet($remote_ip, $remote_port, ...$bytes);

                echo "New Packet: " . (Util::bits2int(...$packet->getHeader()->getId())) . " from: " . $remote_ip . "\n";

                $this->resolver->resolve($this, $packet);
            }
        }
    }

    public function sendPacket(Packet $packet){
        $packet_bytes = Util::bits2bytes(...$packet->toBits());
        $index_shifted_bytes = []; //@todo Refactor into Util class
        for ($i = 0; $i < sizeof($packet_bytes); $i++) {
            $index_shifted_bytes[$i + 1] = $packet_bytes[$i];
        }

        $response_buffer = call_user_func_array("pack", array_merge(["C*"], $index_shifted_bytes));

        socket_sendto($this->socket, $response_buffer, 512, 0, $packet->getRemoteIp(), $packet->getRemotePort());
    }

}