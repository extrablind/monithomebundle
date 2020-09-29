<?php

namespace Extrablind\MonitHomeBundle\Events\ScheduleTrigger;

use Symfony\Component\EventDispatcher\Event;

class ScheduleTriggerEvent extends Event
{
    public function __construct($triggers)
    {
        $this->events = $triggers;
    }

    const NAME = 'monithome.schedule.trigger';
}
