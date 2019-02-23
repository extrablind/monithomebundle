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
