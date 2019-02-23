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
