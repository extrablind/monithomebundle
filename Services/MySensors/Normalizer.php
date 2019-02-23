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

use Extrablind\MonitHomeBundle\Entity\Sensor;

class Normalizer
{
    public $protocol;

    public function __construct(Protocol $protocol)
    {
        $this->protocol = $protocol;

        return $this;
    }

    // sensor type = V_xxx, presentationType = S_xxxx
    // From controller to php = normalize
    public function normalize($payload, Sensor $sensor)
    {
        if ('true' === $payload and 'V_STATUS' === $sensor->getSensorValueType()) {
            return true;
        } elseif ('false' === $payload and 'V_STATUS' === $sensor->getSensorValueType()) {
            return false;
        } elseif (false === $payload and 'V_STATUS' === $sensor->getSensorValueType()) {
            return false;
        } elseif (true === $payload and 'V_STATUS' === $sensor->getSensorValueType()) {
            return true;
        }

        return $payload;
    }

    // From php to controller = encode
    public function encode($payload, Sensor $sensor)
    {
        if ('true' === $payload and 'V_STATUS' === $sensor->getSensorValueType()) {
            return '1';
        } elseif ('false' === $payload and 'V_STATUS' === $sensor->getSensorValueType()) {
            return '0';
        } elseif (false === $payload and 'V_STATUS' === $sensor->getSensorValueType()) {
            return '0';
        } elseif (true === $payload and 'V_STATUS' === $sensor->getSensorValueType()) {
            return '1';
        }

        return $payload;
    }

    public function encodeFromValueType($payload, $valueType)
    {
        if ('true' === $payload and 'V_STATUS' === $valueType) {
            return '1';
        } elseif ('false' === $payload and 'V_STATUS' === $valueType) {
            return '0';
        } elseif (false === $payload and 'V_STATUS' === $valueType) {
            return '0';
        } elseif (true === $payload and 'V_STATUS' === $valueType) {
            return '1';
        }

        return $payload;
    }
}
