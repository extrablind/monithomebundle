<?php

namespace Extrablind\MonitHomeBundle\Repository;

class NodeRepository extends \Doctrine\ORM\EntityRepository
{
    public function getNodes()
    {
        $nodes = $this
    ->createQueryBuilder('node')
    ->select(['node', 'sensor'])
    ->join('node.sensors', 'sensor')
    ->getQuery()
    ->getArrayResult(\Doctrine\ORM\Query::HYDRATE_ARRAY)
    ;

        return $nodes;
    }
}
