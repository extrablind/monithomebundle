<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Extrablind\MonitHomeBundle\Controller\API;

use Extrablind\MonitHomeBundle\Entity\Log;
use Extrablind\MonitHomeBundle\Entity\Sensor;
use Extrablind\MonitHomeBundle\Repository\LogRepository;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class LogsController extends FOSRestController
{
    public function pushAction(Request $request)
    {
        $session = $this->container->get('session');
        $from = $session->get('from');
        $to = $session->get('to');
        $logs = $this->get('doctrine')->getRepository(Log::class)->getLogsBetween($from, $to);

        return new JsonResponse([
      'sensors' => $sensors,
    ]);
    }

    public function getLogsAction(Request $request)
    {
        /*
        $from = $request->get('from');
        $to = $request->get('to');
        $this->transformer            = $this->container->get('monithome.log.transformer');
        $this->elasticGanttFormatter  = $this->container->get('monithome.log.formatter.elastic_gantt');
        $this->fullcalendarFormatter  = $this->container->get('monithome.log.formatter.fullcalendar');

        // Get all sensors
        //$rawScalarDatas = $this->container->get('doctrine')
        //->getRepository(Log::class)->getValuesForSensorBetween($sensor, $from, $to);

        # Actuators
        $sensors = $this->get('doctrine')->getRepository(Sensor::class)
        ->findBy(['sensorType' => LogRepository::ACTUATORS]);

        $events = [];
        foreach($sensors as $sensor){
          $acti =  $this->get('doctrine')->getRepository(Log::class)->getSensorDatasBetween($sensor,$from,$to);
          $e = $this->fullcalendarFormatter->transform($acti);
          $events = array_merge($events,$e);
          $labels[] = [
            'id' => $sensor->getId(),
            'title' => $sensor->getTitle(),
            'eventColor' => '#fffaaa'
          ];
        }
        $return = [
          'labels' => $labels,
          'events' => $events,
        ];
        dump($return);
        exit;
        dump($actuators);
        exit;

        dump($actuators);
        exit;
        $scalars = $this->transformer
        ->setStep(false)
        ->setDatas()
        ->setInfos()
        ->transform($rawScalarDatas);

        $return = [
          'actuators' => $actuators,
          'scalars' => $scalars,
        ];
        dump($return);
        exit;
        return [
          'sensors' => $sensors,
          'actuators' => $actuators,
        ];
        $this->transformer = $this->get('monithome.log.transformer');
        $datas = $this->transformer->chartify($logs);

        return new JsonResponse($datas);
        */
    }
}
