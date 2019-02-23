<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Extrablind\MonitHomeBundle\Events\Doctrine;

// for Doctrine < 2.4: use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\Common\EventSubscriber as DoctrineEventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

class EventSubscriber implements DoctrineEventSubscriber
{
    public function __construct($doctrine)
    {
        $this->em = $doctrine->getManager();
    }

    public function getSubscribedEvents()
    {
        return [
      Events::postPersist,
      Events::postUpdate,
    ];
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->index($args);
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $this->index($args);
    }

    public function index(LifecycleEventArgs $args)
    {
    }
}
