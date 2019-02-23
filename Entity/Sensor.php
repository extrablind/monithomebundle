<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Extrablind\MonitHomeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sensor.
 *
 * @ORM\Table(name="monithome_sensors")
 * @ORM\Entity(repositoryClass="Extrablind\MonitHomeBundle\Repository\SensorRepository")
 */
class Sensor
{
    /**
     * sensor have one node.
     *
     * @ORM\ManyToOne(targetEntity="Extrablind\MonitHomeBundle\Entity\Node")
     */
    private $node;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(name="description", type="text", length=255, nullable=true)
     */
    private $description;

    /**
     * @var string
     * @ORM\Column(name="value", type="string", length=255, nullable=true)
     */
    private $value;

    /**
     * @var array
     * @ORM\Column(name="configuration", type="json_array", nullable=true)
     * [icon, unit]
     */
    private $configuration = [];

    /**
     * @var \DateTime
     * @ORM\Column(name="created", type="datetime", nullable=false)
     */
    private $created;

    /**
     * @var \DateTime
     * @ORM\Column(name="updated", type="datetime", nullable=false)
     */
    private $updated;

    /**
     * @var string
     * @ORM\Column(name="unit", type="string", length=255, nullable=true)
     */
    private $unit;

    /**
     * @var int
     * @ORM\Column(name="sensorId", type="integer", nullable=false)
     */
    private $sensorId;

    /**
     * @var string
     * @ORM\Column(name="sensorUniqueIdentifier", type="string", length=255, nullable=true)
     */
    private $sensorUniqueIdentifier;

    /**
     * @var string
     * @ORM\Column(name="sensorType", type="string", length=255, nullable=true)
     */
    private $sensorType;

    /**
     * @var string
     * @ORM\Column(name="sensorValueType", type="string", length=255, nullable=true)
     */
    private $sensorValueType;

    /**
     * @var \DateTime
     * @ORM\Column(name="lastLogDate", type="datetime", nullable=true)
     */
    private $lastLogDate;

    public function build()
    {
        $result = [];

        $class = new \ReflectionClass(__CLASS__);
        foreach ($class->getMethods() as $method) {
            if ('get' === substr($method->name, 0, 3)) {
                $propName = strtolower(substr($method->name, 3, 1)).substr($method->name, 4);
                $result[$propName] = $method->invoke($this);
            }
        }
        $result['lastLogDate']->format('D/M/Y H:i:s');
        $result['updated']->format('D/M/Y H:i:s');
        $result['created']->format('D/M/Y H:i:s');
        unset($result['node']);
        //unset($result['lastLogDate']);
        //unset($result['created']);
        //unset($result['updated']);
        return $result;
    }

    /**
     * Get the value of sensor have one node.
     *
     * @return mixed
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * Set the value of sensor have one node.
     *
     * @param mixed node
     *
     * @return self
     */
    public function setNode($node)
    {
        $this->node = $node;

        return $this;
    }

    /**
     * Get the value of Id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of Id.
     *
     * @param int id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of Title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the value of Title.
     *
     * @param string title
     *
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the value of Description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the value of Description.
     *
     * @param string description
     *
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the value of Value.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set the value of Value.
     *
     * @param string value
     *
     * @return self
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get the value of Configuration.
     *
     * @return array
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Set the value of Configuration.
     *
     * @param array configuration
     *
     * @return self
     */
    public function setConfiguration(array $configuration)
    {
        $this->configuration = $configuration;

        return $this;
    }

    /**
     * Get the value of Created.
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set the value of Created.
     *
     * @param \DateTime created
     *
     * @return self
     */
    public function setCreated(\DateTime $created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get the value of Updated.
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set the value of Updated.
     *
     * @param \DateTime updated
     *
     * @return self
     */
    public function setUpdated(\DateTime $updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get the value of Unit.
     *
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * Set the value of Unit.
     *
     * @param string unit
     *
     * @return self
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;

        return $this;
    }

    /**
     * Get the value of Sensor Id.
     *
     * @return int
     */
    public function getSensorId()
    {
        return $this->sensorId;
    }

    /**
     * Set the value of Sensor Id.
     *
     * @param int sensorId
     *
     * @return self
     */
    public function setSensorId($sensorId)
    {
        $this->sensorId = $sensorId;

        return $this;
    }

    /**
     * Get the value of Sensor Unique Identifier.
     *
     * @return string
     */
    public function getSensorUniqueIdentifier()
    {
        return $this->sensorUniqueIdentifier;
    }

    /**
     * Set the value of Sensor Unique Identifier.
     *
     * @param string sensorUniqueIdentifier
     *
     * @return self
     */
    public function setSensorUniqueIdentifier($sensorUniqueIdentifier)
    {
        $this->sensorUniqueIdentifier = $sensorUniqueIdentifier;

        return $this;
    }

    /**
     * Get the value of Sensor Type.
     *
     * @return string
     */
    public function getSensorType()
    {
        return $this->sensorType;
    }

    /**
     * Set the value of Sensor Type.
     *
     * @param string sensorType
     *
     * @return self
     */
    public function setSensorType($sensorType)
    {
        $this->sensorType = $sensorType;

        return $this;
    }

    /**
     * Get the value of Sensor Value Type.
     *
     * @return string
     */
    public function getSensorValueType()
    {
        return $this->sensorValueType;
    }

    /**
     * Set the value of Sensor Value Type.
     *
     * @param string sensorValueType
     *
     * @return self
     */
    public function setSensorValueType($sensorValueType)
    {
        $this->sensorValueType = $sensorValueType;

        return $this;
    }

    /**
     * Get the value of Last Log Date.
     *
     * @return \DateTime
     */
    public function getLastLogDate()
    {
        return $this->lastLogDate;
    }

    /**
     * Set the value of Last Log Date.
     *
     * @param \DateTime lastLogDate
     *
     * @return self
     */
    public function setLastLogDate(\DateTime $lastLogDate)
    {
        $this->lastLogDate = $lastLogDate;

        return $this;
    }
}
