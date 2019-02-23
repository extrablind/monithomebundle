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

use Extrablind\MonitHomeBundle\Entity\Log;
use Extrablind\MonitHomeBundle\Entity\Sensor;
use Extrablind\MonitHomeBundle\Repository\LogRepository;

class LogsController
{
    public function __construct($container)
    {
        $this->container = $container;
    }

    public function getLogsAction($from, $to)
    {
        $this->transformer = $this->container->get('monithome.log.transformer');
        $this->elasticGanttFormatter = $this->container->get('monithome.log.formatter.elastic_gantt');
        $this->fullcalendarFormatter = $this->container->get('monithome.log.formatter.fullcalendar');
        $this->chartJsTimelineFormatter = $this->container->get('monithome.log.formatter.timeline');

        $rawActuatorsDatas = $this->container->get('doctrine')
    ->getRepository(Log::class)->getActuatorsBetween($from, $to);
        $rawScalarDatas = $this->container->get('doctrine')
    ->getRepository(Log::class)->getScalarBetween($from, $to);

        $actuators = $this->transformer
    ->setStep(true)
    ->setFill(true)
    ->transform($rawActuatorsDatas);
        $scalars = $this->transformer
    ->setFill(false)
    ->setStep(false)
    ->transform($rawScalarDatas);

        // Actuators HISTORY
        $history = [];
        $sensors = $this->container->get('doctrine')->getRepository(Sensor::class)
    ->findBy(['sensorType' => LogRepository::ACTUATORS]);
        foreach ($sensors as $sensor) {
            $act = $this->container->get('doctrine')->getRepository(Log::class)->getSensorDatasBetween($sensor, $from, $to);
            //$history[] =  $this->elasticGanttFormatter->transform($act, $sensor->getId());
        }
        // Chartjs
        foreach ($sensors as $sensor) {
            $datasSensor = $this->container->get('doctrine')->getRepository(Log::class)->getSensorDatasBetween($sensor, $from, $to);
            $datas[]['data'] = $this->chartJsTimelineFormatter->format($datasSensor);
            $labels[] = $sensor->getTitle();
        }
        $history = [
      'labels' => $labels,
      'events' => $datas,
    ];
        $full = [];
        $full['datasets'] = array_merge($scalars['datasets'], $actuators['datasets']);
        $return = [
      'full' => $full,
      'history' => $history,
      'actuators' => $actuators,
      'scalars' => $scalars,
    ];

        return $return;
    }
}
