# Citadel

[![Travis CI Build](https://travis-ci.com/InterferenceObject/citadel.svg?branch=master)](https://travis-ci.com/InterferenceObject/citadel.svg?branch=master)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D7.2-informational.svg)](https://img.shields.io/badge/php-%3E%3D7.2.14-informational.svg)

A DNS server written in PHP that allows you to build your own resolver, cause why not...

```shell
composer require interferenceobject/citadel
```

## Basic Usage
```php
use Citadel\server\DNS;
use Citadel\server\Resolvers\UpstreamResolver;

//This resolver just propogates dns requests to the provided upstream dns server, but you can build your own resolver.
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
```
You can create your own resolvers by implementing the Resolver interface
```php
use Citadel\server\DNS;
use Citadel\server\Packet;
use Citadel\server\Record;
use Citadel\server\Resolver;
use Citadel\server\Resolvers\UpstreamResolver;
use Citadel\server\Util;

class InternalReroutingResolver implements Resolver{

    private $upstream_resolver;

    public function __construct(Resolver $upstream_resolver) {
        $this->upstream_resolver = $upstream_resolver;
    }

    public function resolve(DNS $dns, Packet $packet) {
        if($packet->isQuery()){
            $header = $packet->getHeader();
            if($header->getQuestionCount() > 0){
                $questions = $packet->getQuestions();
                $answers = $packet->getAnswers();
                $original_answer_size = sizeof($answers);
                foreach($questions as $question){
                    switch($question->getName()){
                        case "home.tv":
                            $ip = array_merge(
                                Util::int2bits(192),
                                Util::int2bits(168),
                                Util::int2bits(1),
                                Util::int2bits(20)
                            ); //Local media server IP
                            $answer = new Record("home.tv", $question->getNameBytePosition(), $question->getType(), $question->getClass(), 5000, 4, $ip);
                            $answers []= $answer;
                            break;
                        case "home.cctv":
                            $ip = array_merge(
                                Util::int2bits(192),
                                Util::int2bits(168),
                                Util::int2bits(1),
                                Util::int2bits(21)
                            ); //Local CCTV server IP
                            $answer = new Record("home.cctv", $question->getNameBytePosition(), $question->getType(), $question->getClass(), 5000, 4, $ip);
                            $answers []= $answer;
                            break;
                    }
                }
                if(sizeof($answers) > $original_answer_size){
                    $header->setQueryOrResponse(1);
                    $header->setAnswerCount(sizeof($answers));
                    $packet->setHeader($header);
                    $packet->setAnswers(...$answers);
                    $dns->sendPacket($packet);
                    return;
                }
            }
        }

        $this->upstream_resolver->resolve($dns, $packet);
    }

}

$upstream_resolver = new UpstreamResolver("1.1.1.1");
$resolver = new InternalReroutingResolver($upstream_resolver);
$dns = new DNS($resolver);
```
*The above is just an example and is untested/outdated, please do not copy directly and expect it to work*

## Documentation
DNS requests are made using a UDP packet. The packet has the same format for both requests and responses and consists of a header of 12 bytes, and the remaing body consists of 4 sections of records, these sections are questions, answers, authority and additional. Questions consist of a name, type and class, all other records start with the same fields but have an additional ttl field, length field and a data field. For more indepth information take a look at [Emil Hernvall's DNS guide](https://github.com/EmilHernvall/dnsguide),

#### Class Table
*All classes have the following namespace: **Citadel/server***

| Class      | Description                                                                                                                                                                                   |
|------------------|-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| DNS              | Handles the single-threaded socket server listening for incomming requests and sending out responses.                                                                                         |
| Packet           | Handles all translations for converting raw packets into functional objects.                                                                                                                  |
| Header           | Contains all header fields for the DNS packets as well as extracting header information from raw packets and converting the header into the suitable format for a raw packet                  |
| Record           | Contains all fields for question, answer, authority and additional records as well as extracting records from raw packets and converting the record into the suitable format for a raw packet |
| Resolver         | Provides an interface for processing packets                                                                                                                                                  |
| RecordType       | Enum of record types                                                                                                                                                                          |
| RecordClass      | Enum of record classes                                                                                                                                                                        |
| Util             | Provides utility functions for dealing with bytes, bits and primitive data                                                                                                                    |
| UpstreamResolver | Basic resolver provided for propagating DNS requests to a specified server                                                                                                                    |

## Credits
- [Emil Hernvall](https://github.com/EmilHernvall/dnsguide) Without his guide, this would not have made any sense to me...