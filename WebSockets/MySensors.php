<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Extrablind\MonitHomeBundle\WebSockets;

use Extrablind\MonitHomeBundle\Entity\Node;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class MySensors implements MessageComponentInterface
{
    private $clients;

    public function __construct($container)
    {
        $this->container = $container;
        $this->clients = new \SplObjectStorage();
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        $conn->send(json_encode('Nouvelle connection: Hello '.$conn->resourceId));
    }

    public function onClose(ConnectionInterface $closedConnection)
    {
        $this->clients->detach($closedConnection);
        echo sprintf('Connection #%d has disconnected'.PHP_EOL, $closedConnection->resourceId);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->send('An error has occurred: '.$e->getMessage());
        $conn->close();
    }

    public function onMessage(ConnectionInterface $conn, $message)
    {
        $messageData = json_decode($message);
        if (null === $messageData) {
            return false;
        }

        $action = $messageData->action ?? 'unknown';
        $message = $messageData->message ?? '';

        switch ($action) {
      case 'getNodes':
      $data = $this->setNodes($conn);
      break;
      default:
      $data = [
        'action' => 'error',
        'message' => 'Action not set or not supported yet !',
      ];
      break;
    }

        $conn->send(json_encode($data));

        return true;
    }

    private function setNodes(ConnectionInterface $conn)
    {
        $nodes = $this->container->get('doctrine')->getRepository(Node::class)
    ->getNodes()
    ;

        return [
      'action' => 'setNodes',
      'message' => $nodes,
    ];
    }
}
