<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Extrablind\MonitHomeBundle\Events\NewMessage;

use Extrablind\MonitHomeBundle\Services\MySensors\Message;
use Symfony\Component\EventDispatcher\Event;

class NewMessageEvent extends Event
{
    const NAME = 'monithome.new.message';

    public function __construct(Message $message, $output = null)
    {
        $this->message = $message;
        $this->output = $output;
    }

    public function getMessage()
    {
        return $this->message;
    }
}
