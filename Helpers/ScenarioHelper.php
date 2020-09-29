<?php

namespace Extrablind\MonitHomeBundle\Helpers;

use Extrablind\MonitHomeBundle\Entity\Scenario;
use Extrablind\MonitHomeBundle\Entity\Sensor;

class ScenarioHelper
{
    protected $scenario = null;

    public function __construct($doctrine, $message, $gateway, $normalizer)
    {
        $this->em         = $doctrine->getManager();
        $this->gateway    = $gateway;
        $this->message    = $message;
        $this->normalizer = $normalizer;

        return $this;
    }

    public function setScenario(Scenario $scenario)
    {
        $this->scenario = $scenario;

        return $this;
    }

    public function shouldTrigger()
    {
        if (null === $this->scenario) {
            throw new \Exception("You should call 'setScenario(scenario)' before calling any action of this class ! ");
        }
        // Actual values are same as required actions value : do not trigger as it is in good state
        $isInGoodState = true;
        foreach ($this->scenario->getActions() as $k => $action) {
            $sensor = $this->em->getRepository(Sensor::class)->findOneBy(['id' => $action['sensor']]);

            if ($sensor->getValue() !== $action['value'] && true === $isInGoodState) {
                $isInGoodState = false;
            }
        }

        if ($isInGoodState) {
            return false;
        }

        $shouldTrigger = false;
        foreach ($this->scenario->getConditions() as $k => $condition) {
            $sensor = $this->em->getRepository(Sensor::class)
      ->findOneBy(['id' => $condition['sensor']]);
            if (!$sensor) {
                // TODO : Sensor is missing We should mark whole scenario as falsy ! Add flag in db.
                // Do not throw exception
                throw new \Exception('No sensor found for this scenario, exit.');

                return;
            }
            // $sensors[$k] = $sensor;
            if ('when' === $condition['type']) {
                $shouldTrigger = $this->resolveValue($sensor, $condition);
            } elseif ('and' === $condition['type']) {
                $shouldTrigger = $shouldTrigger && $this->resolveValue($sensor, $condition);
            } elseif ('or' === $condition['type']) {
                $shouldTrigger = $shouldTrigger || $this->resolveValue($sensor, $condition);
            }
        }

        return $shouldTrigger;
    }

    private function resolveValue($sensor, $condition)
    {
        $sensorValue = $this->normalizer->encode($sensor->getValue(), $sensor);

        switch ($condition['operator']) {
      case '>':
      return $sensorValue > $condition['value'];
      break;
      case '>=':
      return $sensorValue >= $condition['value'];
      break;
      case '<':
      return $sensorValue < $condition['value'];
      break;
      case '<=':
      return $sensorValue <= $condition['value'];
      break;
      case '==':
      return $sensorValue === $condition['value'];
      break;
      default:
      throw new \Exception('Operator is not valid');
      // should never happens, mark falsy
    }
    }

    public function trigger()
    {
        // Get actions and trigger !
        // Do actions
        foreach ($this->scenario->getActions() as $action) {
            $sensor = $this->em->getRepository(Sensor::class)->findOneBy(['id' => $action['sensor']]);
            // Add conditions here
            $this->message->nodeId        = $sensor->getNode()->getNodeId();
            $this->message->childSensorId = $sensor->getSensorId();
            $this->message->command       = 'set';
            $this->message->ack           = true;
            $this->message->type          = $sensor->getSensorValueType();
            $this->message->payload       = $action['value'];
            $this->gateway->send($this->message);
        }
        // Update date for the last played sensor
        $this->scenario->setLastPlayed(new \DateTime());
        // push message here
        $this->em->persist($this->scenario);
        $this->em->flush();

        return true;
    }
}
