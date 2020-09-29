<?php

namespace Extrablind\MonitHomeBundle\Controller\WebSocket;

use Extrablind\MonitHomeBundle\Entity\Event;
use Extrablind\MonitHomeBundle\Entity\Scenario;
use Extrablind\MonitHomeBundle\Utilities\RruleUtility;

class EventsController
{
    public function __construct($container)
    {
        $this->container = $container;
    }

    public function removeEvent($id)
    {
        $this->em = $this->container->get('doctrine')->getManager();
        $event    = $this->em->getRepository(Event::class)->find($id);
        $this->em->remove($event);
        $this->em->flush();

        return true;
    }

    public function saveEvent($event, $rule)
    {
        // Begin transaction
        $this->em = $this->container->get('doctrine')->getManager();
        $this->em->getConnection()->beginTransaction();
        $isUpdate = false;
        try {
            $scenario = $this->em->getRepository(Scenario::class)
      ->findOneBy(['id' => $event['extendedProps']['scenario']]);

            // Create or fetch event if it is update
            $dbEvent = new Event();
            if (isset($event['id']) && is_numeric($event['id'])) {
                $dbEvent = $this->em->getRepository(Event::class)
        ->findOneBy(['id' => $event['id']]);
                if ($dbEvent) {
                    $isUpdate = true;
                }
            } else {
                $dbEvent
        ->setCreated(new \DateTime())
        ->setUpdated(new \DateTime());
            }

            $rrule = new RruleUtility();
            $next  = $rrule->getNextDateFromNow($rule);
            // No next date,
            if (!$next) {
                $dbEvent->setNextTrigger(new \DateTime('1970-01-01 00:00:00'));
                $this->em->persist($dbEvent);
                $this->em->flush();

                return;
            }
            $next->setTimezone(new \DateTimeZone('Europe/Paris'));

            // Insert event
            $dbEvent
      ->setTitle($event['title'])
      ->setScenario($scenario)
      ->setColor($event['color'])
      ->setEvent($event)
      ->setUpdated(new \DateTime())
      ->setRule($rule)
      ->setLastTriggeredAt(new \DateTime('1970-01-01 00:00:00'))
      ->setNextTrigger($next)
      ;
            $this->em->persist($dbEvent);
            $this->em->flush();
            // Put id in event object
            $e       = $dbEvent->getEvent();
            $e['id'] = $dbEvent->getId();
            $dbEvent->setEvent($e);
            $this->em->persist($dbEvent);
            $this->em->flush();

            // Return all events
            $events = $this->em->getRepository(Event::class)->findBy([]);
            $this->em->getConnection()->commit();

            return $events;
        } catch (Exception $e) {
            $this->em->getConnection()->rollBack();
            throw $e;
        }
    }
}
