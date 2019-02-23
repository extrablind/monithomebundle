<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
