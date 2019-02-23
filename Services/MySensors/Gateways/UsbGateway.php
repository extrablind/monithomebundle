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

class UsbGateway implements GatewayInterface
{
    private $hasError = false;
    private $hasMessage = false;
    private $message = '';
    private $error = '';
    private $dev = null;
    public $input = '';
    const SEPARATOR = "\n";

    public function __construct($container)
    {
        $this->container = $container;
        $this->params = $this->container->getParameter('extrablind_monit_home')['gateway']['usb'];
    }

    public function send(Message $message)
    {
        $string = $message->build();

        if (!$this->dev) {
            $this->start();
        }

        $datasWrited = dio_write($this->dev, "$string\n");

        if ($datasWrited <= 0) {
            throw new \Exception('The dio_write() failed. Extension dio failed to write in file '.$this->FILE);
        }

        return true;
    }

    public function start()
    {
        if (!\function_exists('dio_open')) {
            throw new \Exception('dio extension not installed or not present in PHP configuration (cli ini file), please check again your installation and configuration for this extension.');
        }

        $this->dev = dio_open($this->params['device'], O_RDWR | O_NOCTTY | O_NONBLOCK);
        dio_fcntl($this->dev, F_SETFL, O_SYNC);
        dio_tcsetattr($this->dev, [
    'baud' => $this->params['baudrate'],
    'bits' => $this->params['bits'],
    'stop' => $this->params['stop'],
    'parity' => $this->params['parity'],
  ]
);
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getError()
    {
        return $this->error;
    }

    public function hasMessage()
    {
        return $this->hasMessage;
    }

    public function stop()
    {
        dio_close($this->dev);

        return $this;
    }

    public function hasError()
    {
        return false;
    }

    public function read()
    {
        $this->hasMessage = false;
        $char = (string) dio_read($this->dev, 1);

        if (false === $char) {
            return;
        } elseif (self::SEPARATOR === $char) {
            $this->message = $this->input;
            // Check message integrity
            if ('' === $this->message) {
                return;
            }
            $this->hasMessage = true;
            $this->message = $this->input;
            $this->input = '';
        } else {
            $this->input .= $char;
        }
    }
}
