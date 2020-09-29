<?php

namespace Extrablind\MonitHomeBundle\WebSockets;

class WsMessage
{
    public $type;
    public $action;
    public $data;

    const TYPE_ACK      = 0;
    const TYPE_PUSH     = 1;
    const TYPE_ACTION   = 2;
    const TYPE_RESPONSE = 3;

    private $types = [
        self::TYPE_ACK      => 'ack',
        self::TYPE_PUSH     => 'push',
        self::TYPE_ACTION   => 'action',
        self::TYPE_RESPONSE => 'response',
    ];

    public function __construct()
    {
    }

    public function reset()
    {
        $this->setType(self::TYPE_ACTION)
    ->setAction(null)
    ->setData([])
    ;
    }

    public function build()
    {
        $set = [
            'msg' => [
                'type'   => $this->getType(),
                'action' => $this->getAction(),
                'data'   => $this->getData(),
            ],
        ];
        $this->reset();

        return $set;
    }

    /**
     * Get the value of Type.
     *
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the value of Type.
     *
     * @param mixed type
     *
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get the value of Action.
     *
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set the value of Action.
     *
     * @param mixed action
     *
     * @return self
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get the value of Data.
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set the value of Data.
     *
     * @param mixed data
     *
     * @return self
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get the value of Types.
     *
     * @return mixed
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * Set the value of Types.
     *
     * @param mixed types
     *
     * @return self
     */
    public function setTypes($types)
    {
        $this->types = $types;

        return $this;
    }
}
