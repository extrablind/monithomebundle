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
 * @ORM\Table(name="monithome_events")
 * @ORM\Entity(repositoryClass="Extrablind\MonitHomeBundle\Repository\EventRepository")
 */
class Event
{
    /**
     * Event are linked to one scenario.
     *
     * @ORM\ManyToOne(targetEntity="Extrablind\MonitHomeBundle\Entity\Scenario")
     */
    private $scenario;
    /**
     * Event are linked to one scenario.
     *
     * @ORM\OneToMany(targetEntity="Extrablind\MonitHomeBundle\Entity\Trigger", mappedBy="event", fetch="EAGER")
     */
    private $triggers;

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
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(name="color", type="string", length=255, nullable=false)
     */
    private $color;

    /**
     * @var string
     * @ORM\Column(name="rule", type="string", length=700, nullable=false)
     */
    private $rule;

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
     * @var \DateTime
     * @ORM\Column(name="nextTrigger", type="datetime", nullable=false)
     */
    private $nextTrigger;

    /**
     * @var \DateTime
     * @ORM\Column(name="lastTriggeredAt", type="datetime", nullable=true)
     */
    private $lastTriggeredAt;

    /**
     * @var array
     * @ORM\Column(name="event", type="json_array", nullable=false)
     * FullCalendar full event representation
     */
    private $event = [];

    /**
     * Get the value of Event are linked to one scenario.
     *
     * @return mixed
     */
    public function getScenario()
    {
        return $this->scenario;
    }

    /**
     * Set the value of Event are linked to one scenario.
     *
     * @param mixed scenario
     *
     * @return self
     */
    public function setScenario($scenario)
    {
        $this->scenario = $scenario;

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
     * Get the value of Color.
     *
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set the value of Color.
     *
     * @param string color
     *
     * @return self
     */
    public function setColor($color)
    {
        $this->color = $color;

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
     * Get the value of Last Triggered At.
     *
     * @return \DateTime
     */
    public function getLastTriggeredAt()
    {
        return $this->lastTriggeredAt;
    }

    /**
     * Set the value of Last Triggered At.
     *
     * @param \DateTime lastTriggeredAt
     *
     * @return self
     */
    public function setLastTriggeredAt(\DateTime $lastTriggeredAt)
    {
        $this->lastTriggeredAt = $lastTriggeredAt;

        return $this;
    }

    /**
     * Get the value of Event.
     *
     * @return array
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Set the value of Event.
     *
     * @param array event
     *
     * @return self
     */
    public function setEvent(array $event)
    {
        $this->event = $event;

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
     * Get the value of Event are linked to one scenario.
     *
     * @return mixed
     */
    public function getTriggers()
    {
        return $this->triggers;
    }

    /**
     * Set the value of Event are linked to one scenario.
     *
     * @param mixed triggers
     *
     * @return self
     */
    public function setTriggers($triggers)
    {
        $this->triggers = $triggers;

        return $this;
    }

    /**
     * Get the value of Next Trigger.
     *
     * @return \DateTime
     */
    public function getNextTrigger()
    {
        return $this->nextTrigger;
    }

    /**
     * Set the value of Next Trigger.
     *
     * @param \DateTime nextTrigger
     *
     * @return self
     */
    public function setNextTrigger(\DateTime $nextTrigger)
    {
        $this->nextTrigger = $nextTrigger;

        return $this;
    }

    /**
     * Get the value of Rule.
     *
     * @return string
     */
    public function getRule()
    {
        return $this->rule;
    }

    /**
     * Set the value of Rule.
     *
     * @param string rule
     *
     * @return self
     */
    public function setRule($rule)
    {
        $this->rule = $rule;

        return $this;
    }
}
