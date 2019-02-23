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
 * Log.
 *
 * @ORM\Table(name="monithome_logs")
 * @ORM\Entity(repositoryClass="Extrablind\MonitHomeBundle\Repository\LogRepository")
 */
class Log
{
    /**
     * Logs have one sensor.
     *
     * @ORM\ManyToOne(targetEntity="Extrablind\MonitHomeBundle\Entity\Sensor")
     */
    private $sensor;

    /**
     * Logs have one sensor.
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
     * @ORM\Column(name="value", type="string", length=255, nullable=false)
     */
    private $value;

    /**
     * @var \DateTime
     * @ORM\Column(name="created", type="datetime", nullable=false)
     */
    private $created;

    /**
     * Get the value of Logs have one sensor.
     *
     * @return mixed
     */
    public function getSensor()
    {
        return $this->sensor;
    }

    /**
     * Set the value of Logs have one sensor.
     *
     * @param mixed sensor
     *
     * @return self
     */
    public function setSensor($sensor)
    {
        $this->sensor = $sensor;

        return $this;
    }

    /**
     * Get the value of Logs have one sensor.
     *
     * @return mixed
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * Set the value of Logs have one sensor.
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
}
