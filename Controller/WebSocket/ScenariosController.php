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

use Extrablind\MonitHomeBundle\Entity\Scenario;
use Extrablind\MonitHomeBundle\Entity\Sensor;

class ScenariosController
{
    public function __construct($container)
    {
        $this->container = $container;
    }

    public function triggerScenarioAction($id)
    {
        $message = $this->container->get('monithome_mysensors_message');
        $gateway = $this->container->get('monithome.gateway');
        $this->em = $this->container->get('doctrine')->getManager();
        $scenario = $this->em->getRepository(Scenario::class)->findOneBy(['id' => $id]);

        // Do actions
        foreach ($scenario->getActions() as $action) {
            $sensor = $this->em->getRepository(Sensor::class)->findOneBy(['id' => $action['sensor']]);
            // Add conditions here
            $message->nodeId = $sensor->getNode()->getNodeId();
            $message->childSensorId = $sensor->getSensorId();
            $message->command = 'set';
            $message->ack = true;
            $message->type = $sensor->getSensorValueType();
            $message->payload = $action['value'];
            $gateway->start();
            $gateway->send($message);
            $gateway->stop();
        }
        // Update date for the last played sensor
        $scenario->setLastPlayed(new \DateTime());
        $this->em->persist($scenario);
        $this->em->flush();

        return [
      'id' => $scenario->getId(),
      'name' => $scenario->getName(),
      'lastPlayed' => [
        'date' => $scenario->getLastPlayed()->format('Y-m-d H:i:s'),
      ],
    ];
    }

    public function changeScenariosOrderAction($scenarios)
    {
        $this->em = $this->container->get('doctrine')->getManager();
        // Do actions$
        $repo = $this->em->getRepository(Scenario::class);
        foreach ($scenarios as $k => $scenario) {
            $scenar = $repo->findOneBy(['id' => $scenario['id']]);
            $scenar->setPosition((int) $k);
            $this->em->persist($scenar);
            $this->em->flush();
        }
    }

    public function saveScenarioAction($scenario)
    {
        $this->em = $this->container->get('doctrine')->getManager();

        $scenar = new Scenario();
        if (isset($scenario['id']) && is_numeric($scenario['id'])) {
            $scenar = $this->em->getRepository(Scenario::class)
      ->findOneBy(['id' => $scenario['id']]);
        } else {
            $scenar->setCreated(new \DateTime());
            $scenar->setLastPlayed(new \DateTime());
        }
        $scenar
    ->setName($scenario['name'])
    ->setType($scenario['type'])
    ->setActive('true' === $scenario['active'])
    ->setDescription($scenario['description'])
    ->setActions($scenario['actions'])
    ;
        if ('scenario' === $scenario['type']) {
            $scenar->setConditions($scenario['conditions']);
        }
        $this->em->persist($scenar);
        $this->em->flush();

        $inserted = $this->em->getRepository(Scenario::class)
    ->getById($scenar->getId())
    ->getArrayResult(\Doctrine\ORM\Query::HYDRATE_ARRAY)
    ;

        return $inserted;
    }

    public function deleteScenarioAction($scenario)
    {
        $this->em = $this->container
    ->get('doctrine')->getManager();

        $scenar = $this->container->get('doctrine')->getRepository(Scenario::class)
    ->findOneBy(['id' => $scenario['id']]);

        $this->em->remove($scenar);
        $this->em->flush();
    }
}
