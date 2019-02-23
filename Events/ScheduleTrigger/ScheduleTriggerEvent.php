<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
