<?php

namespace Extrablind\MonitHomeBundle\Repository;

class SettingRepository extends \Doctrine\ORM\EntityRepository
{
    public function get()
    {
        $query = $this
    ->createQueryBuilder('setting')
    ->select(['setting'])
    ->setMaxResults(1)
    ->getQuery()
    ;

        return $query;
    }
}
