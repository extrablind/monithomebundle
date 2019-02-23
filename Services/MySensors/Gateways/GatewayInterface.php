<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Extrablind\MonitHomeBundle\Services\MySensors\Gateways;

use  Extrablind\MonitHomeBundle\Services\MySensors\Message;

interface GatewayInterface
{
    public function send(Message $message);

    public function read();

    public function start();

    public function stop();
}
