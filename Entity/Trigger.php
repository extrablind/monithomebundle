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
 * @ORM\Table(name="monithome_triggers")
 * @ORM\Entity(repositoryClass="Extrablind\MonitHomeBundle\Repository\TriggerRepository")
 */
class Trigger
{
    /**
     * Trigger need to know about what action to trigger.
     *
     * @ORM\ManyToOne(targetEntity="Extrablind\MonitHomeBundle\Entity\Event")
     */
    private $event;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     * @ORM\Column(name="date", type="datetime", length=255, nullable=false)
     */
    private $date;

    /**
     * @var \DateTime
     * @ORM\Column(name="triggeredAt", type="datetime", nullable=true)
     */
    private $triggeredAt;

    /**
     * Get the value of Trigger need to know about what action to trigger.
     *
     * @return mixed
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Set the value of Trigger need to know about what action to trigger.
     *
     * @param mixed event
     *
     * @return self
     */
    public function setEvent($event)
    {
        $this->event = $event;

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
     * Get the value of Date.
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set the value of Date.
     *
     * @param \DateTime date
     *
     * @return self
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;

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
     * Get the value of Triggered At.
     *
     * @return \DateTime
     */
    public function getTriggeredAt()
    {
        return $this->triggeredAt;
    }

    /**
     * Set the value of Triggered At.
     *
     * @param \DateTime triggeredAt
     *
     * @return self
     */
    public function setTriggeredAt(\DateTime $triggeredAt)
    {
        $this->triggeredAt = $triggeredAt;

        return $this;
    }

    /**
     * Get the value of Is Triggered.
     *
     * @return bool
     */
    public function getIsTriggered()
    {
        return $this->isTriggered;
    }

    /**
     * Set the value of Is Triggered.
     *
     * @param bool isTriggered
     *
     * @return self
     */
    public function setIsTriggered($isTriggered)
    {
        $this->isTriggered = $isTriggered;

        return $this;
    }
}
