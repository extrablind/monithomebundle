<?php

namespace Extrablind\MonitHomeBundle\Controller\API;

use Extrablind\MonitHomeBundle\Entity\Scenario;
use Extrablind\MonitHomeBundle\Entity\Sensor;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;

class ScenariosController extends FOSRestController
{
    public function __construct()
    {
    }

    /**
     * @Post
     */
    public function triggerScenarioAction(Request $request)
    {
        $scenario = $request->get('scenario');
        $message  = $this->get('monithome_mysensors_message');
        $gateway  = $this->get('monithome.gateway');
        $this->em = $this->container->get('doctrine')->getManager();

        // Do actions
        foreach ($scenario['actions'] as $action) {
            $sensor = $this->em->getRepository(Sensor::class)->findOneBy(['id' => $action['sensor']]);
            // Add conditions here
            $message->nodeId        = $sensor->getNode()->getNodeId();
            $message->childSensorId = $sensor->getSensorId();
            $message->command       = 'set';
            $message->ack           = true;
            $message->type          = $sensor->getSensorValueType();
            $message->payload       = $action['value'];
            $gateway->start();
            $gateway->send($message);
            $gateway->stop();
        }
        // Update date for the last played sensor
        $scenar = $this->em
    ->getRepository(Scenario::class)->findOneBy(['id' => $scenario['id']]);
        $scenar->setLastPlayed(new \DateTime())
    ;

        $this->em->persist($scenar);
        $this->em->flush();

        $view = $this->view([], 200);

        return $this->handleView($view);
    }

    /**
     * @Post
     */
    public function changeScenariosOrderAction(Request $request)
    {
        $scenarios = $request->get('scenarios');
        $this->em  = $this->container->get('doctrine')->getManager();
        // Do actions$
        $repo = $this->em->getRepository(Scenario::class);
        foreach ($scenarios as $k => $scenario) {
            $scenar = $repo->findOneBy(['id' => $scenario['id']]);
            $scenar->setPosition((int) $k);
            $this->em->persist($scenar);
            $this->em->flush();
        }
        $return = 'Ok';
        $view   = $this->view($return, 200);

        return $this->handleView($view);
    }

    /**
     * @Put
     */
    public function putScenarioAction(Request $request)
    {
        $this->em = $this->container
    ->get('doctrine')->getManager();

        $scenario = $request->get('scenario');
        $scenar   = $this->get('doctrine')
    ->getRepository(Scenario::class)->findOneBy(['id' => $scenario['id']]);

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

        return $this->handleView($this->view($scenar, 200));
    }

    /**
     * @Delete
     */
    public function deleteScenarioAction(Request $request)
    {
        $this->em = $this->container
    ->get('doctrine')->getManager();

        $scenario = $request->get('scenario');
        $scenar   = $this->get('doctrine')->getRepository(Scenario::class)
    ->findOneBy(['id' => $scenario['id']]);

        $this->em->remove($scenar);
        $this->em->flush();

        return $this->handleView($this->view($scenar, 200));
    }

    /**
     * @Post
     */
    public function postScenarioAction(Request $request)
    {
        $this->em = $this->container->get('doctrine')->getManager();
        $scenario = $request->get('scenario');
        $save     = [];
        $scenar   = new Scenario();
        $scenar
    ->setName($scenario['name'])
    ->setType($scenario['type'])
    ->setActive('true' === $scenario['active'])
    ->setDescription($scenario['description'])
    ->setActions($scenario['actions'])
    ->setCreated(new \DateTime())
    ;
        if ('scenario' === $scenario['type']) {
            $scenar->setConditions($scenario['conditions']);
        }

        $this->em->persist($scenar);
        $this->em->flush();

        return $this->handleView($this->view($scenar, 200));
    }

    public function getScenariosAction(Request $request)
    {
        $type      = $request->get('type');
        $scenarios = $this->get('doctrine')
    ->getRepository(Scenario::class)
    ->getAll($type)
    ->getArrayResult(\Doctrine\ORM\Query::HYDRATE_ARRAY)
    ;
        $view = $this->view($scenarios, 200);

        return $this->handleView($view);
    }
}
