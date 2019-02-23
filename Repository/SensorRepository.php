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

class SensorRepository extends \Doctrine\ORM\EntityRepository
{
    public function getSensors()
    {
        $sensors = $this
    ->createQueryBuilder('sensor')
    ->select(['node', 'sensor'])
    ->join('sensor.node', 'node')
    ->orderBy('node.place, sensor.sensorType')
    ->getQuery()
    ->getArrayResult(\Doctrine\ORM\Query::HYDRATE_ARRAY)
    ;

        return $sensors;
    }

    public function getOneByIdentifier($identifier)
    {
        return  $this
    ->createQueryBuilder('sensor')
    ->select(['sensor', 'node'])
    ->join('sensor.node', 'node')
    ->where('sensor.sensorUniqueIdentifier = :id')
    ->setParameter('id', $identifier)
    ->getQuery()
    ;
    }
}
