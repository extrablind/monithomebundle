<?php

namespace Extrablind\MonitHomeBundle\Services\MySensors\Gateways;

use  Extrablind\MonitHomeBundle\Services\MySensors\Message;

interface GatewayInterface
{
    public function send(Message $message);

    public function read();

    public function start();

    public function stop();
}
