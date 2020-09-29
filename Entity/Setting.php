<?php

namespace Extrablind\MonitHomeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Node.
 *
 * @ORM\Table(name="monithome_settings")
 * @ORM\Entity(repositoryClass="Extrablind\MonitHomeBundle\Repository\SettingRepository")
 */
class Setting
{
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
     * @ORM\Column(name="metric", type="boolean", length=255, nullable=false)
     */
    private $metric = true;

    /**
     * @var int
     * @ORM\Column(name="autoMode", type="boolean", nullable=false)
     */
    private $autoMode = true;

    /**
     * @var string
     * @ORM\Column(name="timezone", type="string", length=255, nullable=false)
     */
    private $timezone = 'Europe/Paris';

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
     * Get the value of Metric.
     *
     * @return string
     */
    public function getMetric()
    {
        return $this->metric;
    }

    /**
     * Set the value of Metric.
     *
     * @param string metric
     *
     * @return self
     */
    public function setMetric($metric)
    {
        $this->metric = $metric;

        return $this;
    }

    /**
     * Get the value of Auto Mode.
     *
     * @return int
     */
    public function getAutoMode()
    {
        return $this->autoMode;
    }

    /**
     * Set the value of Auto Mode.
     *
     * @param int autoMode
     *
     * @return self
     */
    public function setAutoMode($autoMode)
    {
        $this->autoMode = $autoMode;

        return $this;
    }

    /**
     * Get the value of Timezone.
     *
     * @return string
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * Set the value of Timezone.
     *
     * @param string timezone
     *
     * @return self
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;

        return $this;
    }
}
