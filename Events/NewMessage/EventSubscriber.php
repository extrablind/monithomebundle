<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Extrablind\MonitHomeBundle\Events\NewMessage;

use Extrablind\MonitHomeBundle\Entity\Log;
use Extrablind\MonitHomeBundle\Entity\Node;
use Extrablind\MonitHomeBundle\Entity\Scenario;
use Extrablind\MonitHomeBundle\Entity\Sensor;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EventSubscriber implements EventSubscriberInterface
{
    private $message;
    private $node;
    private $sensor;

    public function __construct($doctrine, $wamp, $scenarioHelper)
    {
        $this->em = $doctrine->getManager();
        $this->wamp = $wamp;
        $this->scenarioHelper = $scenarioHelper;
    }

    public static function getSubscribedEvents()
    {
        return [
      NewMessageEvent::NAME => [
        ['save', 10],
        ['logValueInDb', 20],
        //['treateScenarios', 99],
        ['debug', 100],
      ],
    ];
    }

    public function treateScenarios()
    {
        // As soon as a value changed , evaluate scenario and trigger if needed

        // Clear cached em to report good entity
        $this->em->clear();

        $scenarios = $this->em
    ->getRepository(Scenario::class)
    ->findBy(['type' => 'scenario', 'active' => true]);

        foreach ($scenarios as $k => $s) {
            $this->scenarioHelper->setScenario($s);
            if (!$this->scenarioHelper->shouldTrigger()) {
                continue;
            }
            $this->scenarioHelper->trigger();
        }
    }

    private function shouldLog()
    {
        $conf = $this->sensor->getConfiguration();
        // No log for this sensor
        if ('off' === $conf['log']['status']) {
            return false;
        }
        // Check log mode
        switch ($conf['log']['mode']) {
      // Always log
      case 'always':
      return true;
      // On value change
      case 'onChange':
      return $this->sensor->getValue() !== $this->message->payload;
      // Every xx
      case 'temporality':
      $lastLog = $this->sensor->getLastLogDate();
      // No log date yet, set a date in past
      if (null === $lastLog) {
          $lastLog = new \DateTime('1970-01-01 00:00:00');
      }
      // $unit = $conf['log']['temporality']['unit'];
      $now = new \DateTime();
      $diff = $lastLog->diff($now);
      $minutes = ($diff->days * 24 * 60) + ($diff->h * 60) + $diff->i;

      return $minutes >= $conf['log']['temporality']['every'];
    }

        return false;
    }

    public function debug(NewMessageEvent $event)
    {
        // No debug
        if (null === $event->output or !$event->output->isDebug()) {
            return;
        }
        $this->extract($event);
        $date = date('Y-m-d H:i:s');
        $message = $this->message;
        $sensor = $this->sensor;
        $node = $this->node;

        if (!$message) {
            return;
        }
        if (255 === $message->nodeId) {
            $event->output->writeln("<title>► Send a broadcast message : {$message->payload}</title>");
            $event->output->writeln("  ∟ {$message->log}");

            return;
        }
        // Is controller
        if (0 === $message->nodeId or 255 === $message->childSensorId) {
            $event->output->writeln("<title>► Received debug Message : {$message->payload}</title>");
            $event->output->writeln("  ∟ {$message->log}");

            return;
        }
        if (!$node) {
            $event->output->writeln('<error>► ERROR</error>');
            $event->output->writeln("<error>Unknow node {$message->nodeId} not found</error>");

            return;
        }
        if (!$sensor) {
            $event->output->writeln('<error>► ERROR</error>');
            $event->output->writeln("<error>Unknow sensor {$message->childSensorId}  not found</error>");

            return;
        }

        $event->output->writeln("<title>► Received message : {$message->getRawMessage()}</title>");
        $event->output->writeln("<info>  ∟ Date : $date</>");

        // Sensor
        $event->output->writeln('<info>  ∟ Sensor</info>');
        $event->output->writeln("<info>    ∟ Id: <comment>{$sensor->getId()}</comment></info>");
        $event->output->writeln("<info>    ∟ MySensorId: <comment>{$message->childSensorId}</comment></info>");
        $event->output->writeln("<info>    ∟ Type: <comment>{$message->type}</comment></info>");
        $event->output->writeln("<info>    ∟ Value type: <comment>{$sensor->getSensorValueType()}</comment></info>");
        $event->output->writeln("<info>    ∟ Value: <comment>{$message->payload}</comment></info>");
        $event->output->writeln("<info>    ∟ Previous value: <comment>{$sensor->getValue()}</comment></info>");

        // Node
        $event->output->writeln('<info>  ∟ Node</info>');
        $event->output->writeln("<info>    ∟ Id: <comment>{$message->nodeId}</comment></info>");
        $event->output->writeln("<info>    ∟ Type: <comment>{$node->getNodeType()}</comment></info>");
        $event->output->writeln("<info>    ∟ Place: <comment>{$node->getPlace()}</comment></info>");
        $event->output->writeln("<info>    ∟ Sketch: <comment>{$node->getNodeName()} ({$node->getNodeSketchVersion()})</comment></info>");

        // Logs
        $event->output->writeln('<info>  ∟ Logs</info>');

        if (isset($this->sensor->getConfiguration()['log']['mode'])) {
            $mode = $this->sensor->getConfiguration()['log']['mode'];
            $event->output->writeln("<info>    ∟ Mode: <comment>{$mode}</comment></info>");
        }

        if (!$this->shouldLog($sensor, $message)) {
            $event->output->writeln('<info>    ∟ Status: <comment>No log due for the moment</comment></info>');
        } else {
            $event->output->writeln('<info>    ∟ Status: <comment>Will be logged</comment></info>');
        }
        if ($sensor->getLastLogDate()) {
            $event->output->writeln("<info>    ∟ Last logged: <comment>{$sensor->getLastLogDate()->format('Y-m-d H:i:s')}</comment></info>");
        } else {
            $event->output->writeln('<info>    ∟ Last logged: <comment>Never</comment></info>');
        }
        $event->output->writeln('');
    }

    private function extract(NewMessageEvent $event)
    {
        $this->message = $event->getMessage();

        $this->sensor = $this->em
    ->getRepository(Sensor::class)
    ->findOneBy(['sensorUniqueIdentifier' => "{$this->message->nodeId}-{$this->message->childSensorId}"]);

        $this->node = $this->em->getRepository(Node::class)
    ->findOneBy(['nodeId' => $this->message->nodeId]);
    }

    public function logValueInDb(NewMessageEvent $event)
    {
        $this->extract($event);
        if (!$this->node or !$this->sensor or !$this->message) {
            return;
        }
        if (!$this->shouldLog()) {
            return;
        }

        $log = new Log();
        $log->setValue($this->message->payload)
    ->setCreated(new \DateTime())
    ->setNode($this->node)
    ->setSensor($this->sensor)
    ;
        $this->sensor->setLastLogDate(new \DateTime());
        $this->em->persist($log);
    }

    public function save(NewMessageEvent $event)
    {
        $this->extract($event);
        if (!$this->node or !$this->sensor or !$this->message) {
            return;
        }

        // Sensor : no value change, save nothing
        if ($this->sensor->getValue() === $this->message->payload) {
            return;
        }

        $this->sensor
    ->setValue($this->message->payload)
    ->setUpdated(new \DateTime());

        // Save
        $this->em->persist($this->sensor);
        $this->em->flush();
        $this->em->clear();
        $this->treateScenarios();
        // Push
        $s = [
      'id' => $this->sensor->getId(),
      'value' => $this->message->payload,
      'updated' => [
        'date' => $this->sensor->getUpdated()->format('Y-m-d H:i:s'),
      ],
    ];
        $this->wamp->push(['msg' => ['type' => 'push', 'action' => 'updateSensor', 'data' => $s]], 'monithome_push_topic');
    }
}
