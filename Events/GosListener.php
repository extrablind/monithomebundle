<?php

namespace Extrablind\MonitHomeBundle\Events;

use Gos\Bundle\WebSocketBundle\Event\ClientErrorEvent;
use Gos\Bundle\WebSocketBundle\Event\ClientEvent;
use Gos\Bundle\WebSocketBundle\Event\ClientRejectedEvent;
use Gos\Bundle\WebSocketBundle\Event\ServerEvent;

class GosListener
{
    /**
     * Called whenever a client connects.
     */
    public function onClientConnect(ClientEvent $event)
    {
        $conn = $event->getConnection();

        echo $conn->resourceId.' connected'.PHP_EOL;
    }

    /**
     * Called whenever a client disconnects.
     */
    public function onClientDisconnect(ClientEvent $event)
    {
        $conn = $event->getConnection();

        echo $conn->resourceId.' disconnected'.PHP_EOL;
    }

    /**
     * Called whenever a client errors.
     */
    public function onClientError(ClientErrorEvent $event)
    {
        $conn = $event->getConnection();
        $e    = $event->getException();

        echo 'connection error occurred: '.$e->getMessage().PHP_EOL;
    }

    /**
     * Called whenever server start.
     */
    public function onServerStart(ServerEvent $event)
    {
        $event = $event->getEventLoop();

        echo 'Server was successfully started !'.PHP_EOL;
    }

    /**
     * Called whenever client is rejected by application.
     */
    public function onClientRejected(ClientRejectedEvent $event)
    {
        $origin = $event->getOrigin;

        echo 'connection rejected from '.$origin.PHP_EOL;
    }
}
