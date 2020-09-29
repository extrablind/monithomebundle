<?php

namespace Extrablind\MonitHomeBundle\Controller\API;

use Extrablind\MonitHomeBundle\Entity\Sensor;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;

class SensorsController extends FOSRestController
{
    /**
     * @Post("/sensor")
     * /api/{version}/sensor.{_format}
     */
    public function setSensorAction(Request $request)
    {
        $sensor  = $request->request->get('sensor');
        $node    = $request->request->get('node');
        $message = $this->get('monithome_mysensors_message');
        $gateway = $this->get('monithome.gateway');

        $message->nodeId        = $node['nodeId'];
        $message->childSensorId = $sensor['sensorId'];
        $message->command       = 'set';
        $message->ack           = true;
        $message->type          = $sensor['sensorCommandType'];
        $message->payload       = $sensor['value'];

        $gateway->start();
        $gateway->send($message);
        $gateway->stop();

        $env = $this->container->getParameter('kernel.environment');

        $sensor = $this->get('doctrine')->getRepository(Sensor::class)->findOneBy(['id' => $sensor['id']]);

        return $this->handleView($this->view($sensor, 200));
    }

    /**
     * @Get
     */
    public function getSensorsAction()
    {
        $sensors = $this->get('doctrine')
      ->getRepository(Sensor::class)
      ->getSensors()
    ;
        $view = $this->view($sensors, 200);

        return $this->handleView($view);
    }
}
