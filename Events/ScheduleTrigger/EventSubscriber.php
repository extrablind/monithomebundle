<?php

namespace Extrablind\MonitHomeBundle\Events\ScheduleTrigger;

use Extrablind\MonitHomeBundle\Entity\Sensor;
use Extrablind\MonitHomeBundle\Services\MySensors\Message;
use Extrablind\MonitHomeBundle\Utilities\RruleUtility;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EventSubscriber implements EventSubscriberInterface
{
    public function __construct($doctrine, $wamp, $container)
    {
        $this->em        = $doctrine->getManager();
        $this->wamp      = $wamp;
        $this->container = $container;
    }

    public static function getSubscribedEvents()
    {
        return [
            ScheduleTriggerEvent::NAME => [
                ['trigger', 1],
            ],
        ];
    }

    public function trigger(ScheduleTriggerEvent $event)
    {
        foreach ($event->events as $evt) {
            $rrule = new RruleUtility();
            $next  = $rrule->getNextDateFromNow($evt->getRule(), 1);

            // No next date,
            if (!$next) {
                $evt->setNextTrigger(new \DateTime('1970-01-01 00:00:00'));
                $this->em->persist($evt);
                $this->em->flush();

                return;
            }
            $next->setTimezone(new \DateTimeZone('Europe/Paris'));

            // TODO:  This should be injected !!
            $message  = $this->container->get('monithome_mysensors_message');
            $gateway  = $this->container->get('monithome.gateway');
            $scenario = $evt->getScenario();
            // Do actions
            // Do not start gateway, gateway already started we are in loop
            foreach ($scenario->getActions() as $action) {
                $sensor = $this->em->getRepository(Sensor::class)->findOneBy(['id' => $action['sensor']]);
                // Add conditions here
                $message->nodeId        = $sensor->getNode()->getNodeId();
                $message->childSensorId = $sensor->getSensorId();
                $message->command       = 'set';
                $message->ack           = true;
                $message->type          = $sensor->getSensorValueType();
                $message->payload       = $action['value'];
                $gateway->send($message);
            }
            $evt
      ->setNextTrigger($next)
      ->setLastTriggeredAt(new \DateTime())
      ;

            // Push a message to gui
            $this->wamp->push(['msg' => ['type' => 'push', 'action' => 'flash', 'data' => 'Action Just been triggered']], 'monithome_push_topic');

            // Save
            $this->em->persist($evt);
            $this->em->flush();
        }
    }
}
