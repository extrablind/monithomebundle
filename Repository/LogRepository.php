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

class LogRepository extends \Doctrine\ORM\EntityRepository
{
    public const SCALARS = ['S_HUM', 'S_TEMP'];
    public const ACTUATORS = ['S_BINARY'];

    public function getSensorDatasBetween($sensor, $from, $to)
    {
        $results = $this
    ->createQueryBuilder('log')
    ->select(['log.value', 'log.created', 'sensor.id', 'sensor.title', 'node.place', 'sensor.sensorValueType', 'sensor.sensorType'])
    ->join('log.sensor', 'sensor')
    ->join('log.node', 'node')
    ->where('log.created BETWEEN :from AND :to')
    ->andWhere('sensor = :sensor')
    ->orderBy('log.created')
    ->setParameter('from', $from)
    ->setParameter('to', $to)
    ->setParameter('sensor', $sensor)
    ->getQuery()
    ->getArrayResult(\Doctrine\ORM\Query::HYDRATE_ARRAY)
    ;

        return $results;
    }

    public function getActuatorsBetween($from, $to)
    {
        $actuators = $this
    ->createQueryBuilder('log')
    ->select(['log.value', 'log.created', 'sensor.id', 'sensor.title', 'node.place', 'sensor.sensorValueType', 'sensor.sensorType'])
    ->join('log.sensor', 'sensor')
    ->join('log.node', 'node')
    ->where('log.created BETWEEN :from AND :to')
    ->andWhere('sensor.sensorType IN (:actuators)')
    ->orderBy('log.created, sensor.id')
    ->setParameter('from', $from)
    ->setParameter('to', $to)
    ->setParameter('actuators', self::ACTUATORS)
    ->getQuery()
    ->getArrayResult(\Doctrine\ORM\Query::HYDRATE_ARRAY)
    ;

        return $actuators;
    }

    public function getScalarBetween($from, $to, $limit = 200)
    {
        $qb = $this->createQueryBuilder('log')
    ->select(['log.value', 'log.created', 'sensor.id', 'sensor.title', 'node.place', 'sensor.sensorValueType', 'sensor.sensorType'])
    ->join('log.sensor', 'sensor')
    ->join('log.node', 'node')
    ->where('log.created BETWEEN :from AND :to')
    ->andWhere('sensor.sensorType IN (:scalars)')
    ->setParameter('scalars', self::SCALARS)
    ->setParameter('from', $from)
    ->setParameter('to', $to)
    ;
        $results = $qb->getQuery()->getArrayResult(\Doctrine\ORM\Query::HYDRATE_ARRAY)
    ;

        return $results;
    }
}
