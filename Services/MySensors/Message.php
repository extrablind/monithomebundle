<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Extrablind\MonitHomeBundle\Services\MySensors;

class Message
{
    public $nodeId;
    public $childSensorId;
    public $command;
    public $ack;
    public $type;
    public $payload;
    public $log;
    public $isBuilded = false;
    public $raw = '';

    public $normalizer;
    public $protocol;

    public function __construct(Protocol $protocol, Normalizer $normalizer)
    {
        $this->protocol = $protocol;
        $this->normalizer = $normalizer;
    }

    /**
     * Auto get/set.
     */
    public function __call($method, $params)
    {
        $var = lcfirst(substr($method, 3));
        if (0 === strncasecmp($method, 'get', 3)) {
            return $this->$var;
        }
        if (0 === strncasecmp($method, 'set', 3)) {
            $this->$var = $params[0];
        }
    }

    public function reset()
    {
        $this->nodeId = null;
        $this->childSensorId = null;
        $this->command = null;
        $this->ack = null;
        $this->type = null;
        $this->payload = null;
        $this->log = null;
        $this->isBuilded = false;
        $this->raw = null;
    }

    public function getCommandType()
    {
        $commandType = (\in_array($this->command, ['set', 'req', 'stream'])) ? 'subtypes' : $this->command;

        return array_flip($this->protocol::TYPES[$commandType])[$this->type];
    }

    public function build()
    {
        $command['node-id'] = (string) $this->nodeId;
        $command['child-sensor-id'] = (string) $this->childSensorId;
        $command['command'] = (string) $this->protocol->find('COMMANDS', $this->command);
        $command['ack'] = (int) $this->ack;
        $command['type'] = (string) $this->getCommandType();
        $command['payload'] = $this->normalizer->encodeFromValueType($this->payload, $this->type);
        $command = (string) implode(';', $command);
        $this->builded = $command;

        return $command;
    }

    public function getRawMessage()
    {
        return $this->raw;
    }

    public function parse($message)
    {
        $this->reset();
        $this->raw = $message;

        // Eliminate empty lines
        if (PHP_EOL === $message) {
            return false;
        }
        if (empty($message)) {
            return false;
        }
        // Begin parse
        $messageArray = explode(';', $message);
        $count = \count($messageArray);
        // Message should be exact to 5 values separated by commas
        if (6 !== $count) {
            return false;
        }
        list($nodeId, $childSensorId, $command, $ack, $type, $payload) = $messageArray;

        $this->message = trim($message);
        $this->nodeId = (string) trim($nodeId);
        $this->childSensorId = (string) $childSensorId;
        $this->command = $this->protocol::COMMANDS[$command];

        // Guess constant array command type
        $this->commandType = (\in_array($this->command, ['set', 'req', 'stream'])) ? 'subtypes' : $this->command;
        $this->ack = ($ack ? true : false);
        $this->payload = (string) str_replace("\n", '', $payload);
        $this->type = $this->protocol::TYPES[$this->commandType][$type];
        $this->isLog = 'I_LOG_MESSAGE' === $this->type;

        if (!$this->isLog) {
            return $this;
        }

        $logParser = $this->protocol->getLogParser();
        foreach ($logParser as $log) {
            preg_match($log['regex'], $this->payload, $matches);
            if (empty($matches)) {
                continue;
            }
            $this->log = $log['explain'];

            // Replace js expressions from explaination string by constants
            //# First search for payload type number in $matches results from js expression
            preg_match('#{pt:\$(\d+)}#', $this->log, $m);
            if (isset($m[1])) {
                $payloadType = (int) $matches[(int) $m[1] + 1];
                if (isset($this->protocol::PAYLOAD[$payloadType])) {
                    $this->log = preg_replace('#{pt:\$(\d+)}#', $this->protocol::PAYLOAD[$payloadType], $this->log);
                    $this->payloadType = $this->protocol::PAYLOAD[$payloadType];
                }
            }
            $this->log = preg_replace('#{type:(\$\d:\$\d)}#', $this->protocol::TYPES[$this->commandType][$type], $this->log);
            $this->log = preg_replace('#{command:\$\d}#', $this->command, $this->log);

            for ($i = 1; $i <= 13; ++$i) {
                if (!isset($matches[$i + 1])) {
                    continue;
                }
                // Replace $vars from regex by matches in explain string
                $this->log = str_replace("$$i", $matches[$i + 1], $this->log);
            }
        }

        return $this;
    }
}
