<?php

namespace Extrablind\MonitHomeBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Node.
 *
 * @ORM\Table(name="monithome_nodes")
 * @ORM\Entity(repositoryClass="Extrablind\MonitHomeBundle\Repository\NodeRepository")
 */
class Node
{
    public function __construct()
    {
        $this->sensors = new ArrayCollection();
    }

    /**
     * Node have many sensors attached to it.
     *
     * @ORM\OneToMany(targetEntity="Extrablind\MonitHomeBundle\Entity\Sensor", mappedBy="node", fetch="EAGER")
     */
    private $sensors;

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
     * @ORM\Column(name="place", type="string", length=255, nullable=false)
     */
    private $place;

    /**
     * @var int
     * @ORM\Column(name="nodeId", type="integer",  nullable=false)
     */
    private $nodeId;

    /**
     * @var string
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var string
     * @ORM\Column(name="nodeType", type="string", length=255, nullable=false)
     */
    private $nodeType;

    /**
     * @var string
     * @ORM\Column(name="nodeName", type="string", length=255, nullable=true)
     */
    private $nodeName;

    /**
     * @var string
     * @ORM\Column(name="nodeSketchVersion", type="string", length=255, nullable=true)
     */
    private $nodeSketchVersion;

    /**
     * @var \DateTime
     * @ORM\Column(name="created", type="datetime", nullable=false)
     */
    private $created;

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
     * Get the value of Place.
     *
     * @return string
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * Set the value of Place.
     *
     * @param string place
     *
     * @return self
     */
    public function setPlace($place)
    {
        $this->place = $place;

        return $this;
    }

    /**
     * Get the value of Node Id.
     *
     * @return string
     */
    public function getNodeId()
    {
        return $this->nodeId;
    }

    /**
     * Set the value of Node Id.
     *
     * @param string nodeId
     *
     * @return self
     */
    public function setNodeId($nodeId)
    {
        $this->nodeId = $nodeId;

        return $this;
    }

    /**
     * Get the value of Node Type.
     *
     * @return string
     */
    public function getNodeType()
    {
        return $this->nodeType;
    }

    /**
     * Set the value of Node Type.
     *
     * @param string nodeType
     *
     * @return self
     */
    public function setNodeType($nodeType)
    {
        $this->nodeType = $nodeType;

        return $this;
    }

    /**
     * Get the value of Node Name.
     *
     * @return string
     */
    public function getNodeName()
    {
        return $this->nodeName;
    }

    /**
     * Set the value of Node Name.
     *
     * @param string nodeName
     *
     * @return self
     */
    public function setNodeName($nodeName)
    {
        $this->nodeName = $nodeName;

        return $this;
    }

    /**
     * Get the value of Node Sketch Version.
     *
     * @return string
     */
    public function getNodeSketchVersion()
    {
        return $this->nodeSketchVersion;
    }

    /**
     * Set the value of Node Sketch Version.
     *
     * @param string nodeSketchVersion
     *
     * @return self
     */
    public function setNodeSketchVersion($nodeSketchVersion)
    {
        $this->nodeSketchVersion = $nodeSketchVersion;

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
     * Get the value of Node have many sensors attached.
     *
     * @return mixed
     */
    public function getSensors()
    {
        return $this->sensors;
    }

    /**
     * Set the value of Node have many sensors attached.
     *
     * @param mixed sensors
     *
     * @return self
     */
    public function setSensors($sensors)
    {
        $this->sensors = $sensors;

        return $this;
    }
}
