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
            if(($data = socket_recvfrom($this->socket, $buffer, 512, 0, $remote_ip, $remote_port)) !== false) {
                $bytes = unpack("C*", $buffer);
                $bits = $this->bytes2bits(...$bytes);

                $header = $this->array_extract($bits, 0, 96);

                $header_index = 0;
                $id = $this->array_extract($header, $header_index, ($header_index += 16));
                $qr = $this->array_extract($header, $header_index, ($header_index += 1));
                $opcode = $this->array_extract($header, $header_index, ($header_index += 4));
                $aa = $this->array_extract($header, $header_index, ($header_index += 1));
                $tc = $this->array_extract($header, $header_index, ($header_index += 1));
                $rd = $this->array_extract($header, $header_index, ($header_index += 1));
                $ra = $this->array_extract($header, $header_index, ($header_index += 1));
                $z = $this->array_extract($header, $header_index, ($header_index += 3));
                $rcode = $this->array_extract($header, $header_index, ($header_index += 4));
                $qdcount = $this->array_extract($header, $header_index, ($header_index += 16));
                $ancount = $this->array_extract($header, $header_index, ($header_index += 16));
                $nscount = $this->array_extract($header, $header_index, ($header_index += 16));
                $arcount = $this->array_extract($header, $header_index, ($header_index += 16));

                echo "ID: " . $this->print_array($id) . "\n";
                echo "QR: " . $this->print_array($qr) . "\n";
                echo "OPCODE: " . $this->print_array($opcode) . "\n";
                echo "AA: " . $this->print_array($aa) . "\n";
                echo "TC: " . $this->print_array($tc) . "\n";
                echo "RD: " . $this->print_array($rd) . "\n";
                echo "RA: " . $this->print_array($ra) . "\n";
                echo "Z: " . $this->print_array($z) . "\n";
                echo "RCODE: " . $this->print_array($rcode) . "\n";
                echo "QDCOUNT: " . $this->print_array($qdcount) . "\n";
                echo "ANCOUNT: " . $this->print_array($ancount) . "\n";
                echo "NSCOUNT: " . $this->print_array($nscount) . "\n";
                echo "ARCOUNT: " . $this->print_array($arcount) . "\n";

                echo "\n";
            }
        }
    }

    private function bytes2bits(int... $bytes){
        $bits = [];
        foreach($bytes as $byte){
            $bit_conversion = decbin($byte);
            $bit_count = strlen($bit_conversion);
            for($i = 0; $i < 8 - $bit_count; $i++){
                $bits []= 0;
            }
            for($i = 0; $i < $bit_count; $i++){
                $bits[]= $bit_conversion[$i];
            }
        }
        return $bits;
    }

    private function array_extract(array $array, int $start = 0, int $length = -1){
        if($start < 0){
            $start = 0;
        }
        if($length < 0){
            $length = sizeof($array);
        }

        $extract = [];

        for($i = $start; $i < $length; $i++){
            $extract[]= $array[$i];
        }

        return $extract;
    }

    private function print_array(array $array){
        $data = "[";
        for($i = 0; $i < sizeof($array); $i++){
            $data .= $array[$i];
            if($i < sizeof($array)-1){
                $data .= ",";
            }
        }
        $data .= "]";
        return $data;
    }

}