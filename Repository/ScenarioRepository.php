<?php

namespace Extrablind\MonitHomeBundle\Repository;

class ScenarioRepository extends \Doctrine\ORM\EntityRepository
{
    public function getAll($type = null)
    {
        $qb = $this->createQueryBuilder('scenario');
        $qb->select(['scenario'])->orderBy('scenario.position');

        if (null !== $type) {
            //$qb->where($qb->expr()->in('scenario.type',$types));
            $qb->where('scenario.type = :type')->setParameter('type', $type);
        }

        return $qb->getQuery();
    }

    public function getById($id)
    {
        return  $this
    ->createQueryBuilder('scenario')
    ->select(['scenario'])
    ->where('scenario.id = :id')
    ->setParameter('id', $id)
    ->getQuery()
    ;
    }
}
