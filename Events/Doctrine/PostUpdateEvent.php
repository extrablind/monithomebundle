<?php

namespace Extrablind\MonitHomeBundle\Events\Doctrine;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Extrablind\MonitHomeBundle\Entity\Node;
use Extrablind\MonitHomeBundle\Entity\Sensor;
use Symfony\Component\EventDispatcher\Event;

class PostUpdateEvent extends Event
{
    const NAME = 'monithome.doctrine.post_update';

    public function __construct($container)
    {
        $this->container = $container;
    }

    // monithome.ws.main
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if (!$entity instanceof Node) {
            return;
        }
        if (!$entity instanceof Sensor) {
            return;
        }
    }
}
