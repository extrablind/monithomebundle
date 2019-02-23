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

use Extrablind\MonitHomeBundle\Entity\Event;
use Extrablind\MonitHomeBundle\Entity\Node;
use Extrablind\MonitHomeBundle\Entity\Scenario;
use Extrablind\MonitHomeBundle\Entity\Sensor;
use Extrablind\MonitHomeBundle\Services\MySensors\Protocol;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Gos\Bundle\WebSocketBundle\Topic\TopicInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;

class InputTopic implements TopicInterface
{
    public function __construct($doctrine, $message, $sensorsController, $scenarioController, $logsController, $eventsController, $settingsController)
    {
        $this->em = $doctrine;
        $this->msg = $message;

        $this->sensorsController = $sensorsController;
        $this->scenarioController = $scenarioController;
        $this->logsController = $logsController;
        $this->eventsController = $eventsController;
        $this->settingsController = $settingsController;
    }

    /**
     * This will receive any Subscription requests for this topic.
     *
     * @param ConnectionInterface $connection
     * @param Topic               $topic
     * @param WampRequest         $request
     */
    public function onSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request)
    {
        //this will broadcast the message to ALL subscribers of this topic.
        $topic->broadcast(['msg' => $connection->resourceId.' has joined push channel'.$topic->getId()]);
    }

    /**
     * This will receive any UnSubscription requests for this topic.
     *
     * @param ConnectionInterface $connection
     * @param Topic               $topic
     * @param WampRequest         $request
     */
    public function onUnSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request)
    {
        //this will broadcast the message to ALL subscribers of this topic.
        $topic->broadcast(['msg' => $connection->resourceId.' has left '.$topic->getId()]);
    }

    /**
     * This will receive any Publish requests for this topic.
     *
     * @param ConnectionInterface $connection
     * @param Topic               $topic
     * @param WampRequest         $request
     * @param $event
     * @param array $exclude
     * @param array $eligible
     *
     * @return mixed|void
     */
    public function onPublish(ConnectionInterface $connection, Topic $topic, WampRequest $request, $event, array $exclude, array $eligible)
    {
        if (\is_string($event)) {
            return;
        }
        $include = [$connection->WAMP->sessionId];
        $exclude = [];

        // Entry point for all input actions
        switch ($event['action']) {
      case 'saveSettings':
      $this->settingsController->saveSettings($event['params']['settings']);

      $settings = $this->settingsController->getSettings();
      $this->msg
      ->setAction('setSettings')
      ->setType(WsMessage::TYPE_ACTION)
      ->setData($settings);
      $topic->broadcast($this->msg->build());

      return;

      case 'getSettings':
      $settings = $this->settingsController->getSettings();
      $this->msg
      ->setAction('setSettings')
      ->setType(WsMessage::TYPE_ACTION)
      ->setData($settings);

      $topic->broadcast($this->msg->build());

      return;

      case 'getSensorTypesList':
      $list = Protocol::TYPES['presentation'];
      asort($list);
      $list = array_values($list);
      $this->msg
      ->setAction('setSensorTypesList')
      ->setType(WsMessage::TYPE_ACTION)
      ->setData($list);
      $topic->broadcast($this->msg->build(), $exclude, $include);

      return;

      case 'getSensorValueTypesList':
      $list = Protocol::TYPES['subtypes'];
      asort($list);
      $list = array_values($list);
      $this->msg
      ->setAction('setSensorValueTypesList')
      ->setType(WsMessage::TYPE_ACTION)
      ->setData($list);
      $topic->broadcast($this->msg->build(), $exclude, $include);

      return;

      case 'removeEvent':
      $this->eventsController->removeEvent($event['params']['id']);
      $events = $this->em->getRepository(Event::class)->getAll()->getArrayResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
      $this->msg
      ->setAction('setEvents')
      ->setType(WsMessage::TYPE_ACTION)
      ->setData($events);
      $topic->broadcast($this->msg->build(), $exclude, $include);

      return;

      case 'getEvents':
      $events = $this->em->getRepository(Event::class)->getAll()->getArrayResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
      $this->msg->setAction('setEvents')->setType(WsMessage::TYPE_ACTION)->setData($events);
      $topic->broadcast($this->msg->build(), $exclude, $include);

      return;

      case 'saveEvent':
      $paramEvent = $event['params']['event'];
      $ruleParam = $event['params']['rule'];
      $events = $this->eventsController->saveEvent($paramEvent, $ruleParam);

      // Response : update event list
      $events = $this->em->getRepository(Event::class)->getAll()->getArrayResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
      $this->msg->setAction('setEvents')->setType(WsMessage::TYPE_ACTION)->setData($events);
      $topic->broadcast($this->msg->build(), $exclude, $include);

      return;

      case 'triggerScenario':
      $scenar = $this->scenarioController->triggerScenarioAction($event['params']['id']);
      $this->msg->setType(WsMessage::TYPE_ACK)->setData(sprintf('A scenario "%s" just been triggered', $scenar['name']));
      $topic->broadcast($this->msg->build());
      $this->msg->setAction('updateScenario')->setType(WsMessage::TYPE_ACK)->setData($scenar);
      $topic->broadcast($this->msg->build());

      return;

      case 'saveSensor':
      $sensor = $this->sensorsController->saveSensor($event['params']['sensor'], $event['params']['node']);
      // Return sensors
      $sensors = $this->em->getRepository(Sensor::class)->getSensors();
      $msg = ['msg' => ['action' => 'setSensors', 'data' => $sensors]];
      $topic->broadcast($msg, $exclude, $include);

      return;

      case 'changeScenariosOrder':
      $this->scenarioController->changeScenariosOrderAction($event['params']['scenarios']);
      $this->msg->setType(WsMessage::TYPE_ACK)->setData('Scenario order changed');
      $topic->broadcast($this->msg->build());

      return;

      case 'deleteScenario':
      $this->scenarioController->deleteScenarioAction($event['params']['scenario']);
      $scenarios = $this->em->getRepository(Scenario::class)
      ->getAll()
      ->getArrayResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
      $this->msg->setAction('setScenarios')->setType(WsMessage::TYPE_ACTION)->setData(['scenarios' => $scenarios]);
      $topic->broadcast($this->msg->build(), $exclude, $include);
      break;

      case 'saveScenario':
      $scenario = $this->scenarioController->saveScenarioAction($event['params']['scenario']);
      $this->msg->setAction('setScenario')->setType(WsMessage::TYPE_ACTION)->setData(['created' => !isset($event['params']['scenario']['id']), 'scenario' => current($scenario)]);
      $topic->broadcast($this->msg->build(), $exclude, $include);
      break;

      // TO only one
      case 'getScenarios':
      $scenarios = $this->em->getRepository(Scenario::class)
      ->getAll()
      ->getArrayResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
      $this->msg->setAction('setScenarios')->setType(WsMessage::TYPE_ACTION)->setData(['scenarios' => $scenarios]);
      $topic->broadcast($this->msg->build(), $exclude, $include);
      break;

      // Actions that does not require response (response handle by push)
      case 'sensorEditValue':
      $this->sensorsController->setSensorAction($event['params']['id'], $event['params']['value']);

      return;

      case 'getLogs':
      $params = $event['params'];
      $logs = $this->logsController->getLogsAction($params['from'], $params['to']);
      $msg = ['msg' => ['action' => 'setLogs', 'data' => ['logs' => ($logs)]]];
      $topic->broadcast($msg, $exclude, $include);
      break;

      // Response sended to user demanding for it
      case 'getSensors':
      $sensors = $this->em->getRepository(Sensor::class)->getSensors();
      $msg = ['msg' => ['action' => 'setSensors', 'data' => $sensors]];
      $topic->broadcast($msg, $exclude, $include);
      break;

      case 'getNodes':
      $nodes = $this->em->getRepository(Node::class)->getNodes();
      $msg = ['msg' => ['action' => 'setNodes', 'data' => $nodes]];
      $topic->broadcast($msg, $exclude, $include);
      break;
    }
    }

    /**
     * Like RPC is will use to prefix the channel.
     *
     * @return string
     */
    public function getName()
    {
        return 'monithome.input.topic';
    }
}
