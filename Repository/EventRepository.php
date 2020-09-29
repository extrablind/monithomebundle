<?php

namespace Extrablind\MonitHomeBundle\Repository;

class EventRepository extends \Doctrine\ORM\EntityRepository
{
    public function getAll()
    {
        $events = $this
    ->createQueryBuilder('event')
    ->select(['event', 'scenario'])
    ->join('event.scenario', 'scenario')
    ->getQuery()
    ;

        return $events;
    }

    public function getByDate($date)
    {
        $events = $this
    ->createQueryBuilder('event')
    ->select(['event'])
    ->where('event.nextTrigger = :date')
    ->setParameter('date', $date)
    ->getQuery()
    ;

        return $events;
    }
}
