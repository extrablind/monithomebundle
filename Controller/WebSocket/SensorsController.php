<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Extrablind\MonitHomeBundle\Controller\WebSocket;

use Extrablind\MonitHomeBundle\Entity\Node;
use Extrablind\MonitHomeBundle\Entity\Sensor;

class SensorsController
{
    public function __construct($container)
    {
        $this->container = $container;
    }

    public function saveSensor($inputSensor, $inputNode)
    {
        $this->em = $this->container->get('doctrine')->getManager();
        $node = $this->em->getRepository(Node::class)
        ->findOneBy(['id' => $inputNode['id']]);

        $sensor = new Sensor();
        if (isset($inputSensor['id']) && is_numeric($inputSensor['id'])) {
            $sensor = $this->em->getRepository(Sensor::class)
          ->findOneBy(['id' => $inputSensor['id']]);
        } else {
            $sensor->setCreated(new \DateTime());
        }
        if (isset($inputSensor['configuration']['unit'])) {
            $sensor->setUnit($inputSensor['configuration']['unit']);
        }

        $sensor
        ->setTitle($inputSensor['title'])
        ->setDescription($inputSensor['description'])
        ->setSensorType($inputSensor['sensorType'])
        ->setSensorValueType($inputSensor['sensorValueType'])
        ->setSensorId($inputSensor['sensorId'])
        ->setSensorUniqueIdentifier($node->getNodeId().'-'.$inputSensor['sensorId'])
        ->setNode($node)
        ->setLastLogDate(new \DateTime())
        ->setUpdated(new \DateTime())
        ;
        $this->em->persist($sensor);
        $this->em->flush();

        /*
        $inserted = $this->em->getRepository(Sensor::class)
        ->getById($sensor->getId())
        ->getArrayResult(\Doctrine\ORM\Query::HYDRATE_ARRAY)
        ;
        */
        return $sensor;
    }

    public function setSensorAction($id, $value)
    {
        $message = $this->container->get('monithome_mysensors_message');
        $gateway = $this->container->get('monithome.gateway');
        $sensor = $this->container->get('doctrine')->getRepository(Sensor::class)
        ->findOneBy(['id' => $id]);

        $message->nodeId = $sensor->getNode()->getNodeId();
        $message->childSensorId = $sensor->getSensorId();
        $message->command = 'set';
        $message->ack = true;
        $message->type = $sensor->getSensorValueType();
        $message->payload = $value;

        $gateway->start();
        $gateway->send($message);
        $gateway->stop();
    }
}
