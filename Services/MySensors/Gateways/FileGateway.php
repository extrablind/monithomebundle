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
use Symfony\Component\Filesystem\Filesystem;

class FileGateway implements GatewayInterface
{
    // FILE should have a definition in parameters
    private $FILE = '';
    private $hasError = false;
    private $hasMessage = false;
    private $message = '';
    private $error = '';
    private $input = '';
    private $params = null;
    private $fs = null;
    const SEPARATOR = "\n";

    public function __construct($container)
    {
        $this->container = $container;
        $this->params = $this->container->getParameter('extrablind_monit_home');

        $this->FILE = $this->params['gateway']['file']['path'];
        $this->fs = new Filesystem();
        if (!$this->fs->exists($this->FILE)) {
            $this->fs->touch($this->FILE);
        }
    }

    public function send(Message $message)
    {
        $string = $message->build();

        return $this->fs->appendToFile($this->FILE, $string.PHP_EOL);
    }

    public function start()
    {
        $this->file = fopen($this->FILE, 'rw');
        if (!$this->file) {
            throw new \Exception('Impossible d\'ouvrir le fichier '.$this->FILE);
        }
    }

    public function stop()
    {
        //ftruncate($this->file, 0);
        //$this->fs->remove($this->FILE);
        fclose($this->file);
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

    public function hasError()
    {
        return false;
    }

    public function read()
    {
        $this->hasMessage = false;
        $char = fgetc($this->file);
        if (false === $char) {
            $this->input = '';

            return;
        } elseif (feof($this->file)) {
            return;
        } elseif (self::SEPARATOR === $char) {
            $this->message = $this->input;
            // Check message integrity
            if (
        self::SEPARATOR === $this->message
        or '' === $this->message
      ) {
                $this->input = '';

                return;
            }
            $this->hasMessage = true;
            $this->input = '';
        } else {
            $this->input .= $char;
        }
    }
}
