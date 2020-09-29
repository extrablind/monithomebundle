<?php

namespace Extrablind\MonitHomeBundle\WebSockets;

use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Gos\Bundle\WebSocketBundle\Topic\PushableTopicInterface;
use Gos\Bundle\WebSocketBundle\Topic\TopicInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;

class PushTopic implements TopicInterface, PushableTopicInterface
{
    public function __construct($doctrine, $sensorsController)
    {
        $this->em                = $doctrine;
        $this->sensorsController = $sensorsController;
    }

    /**
     * This will receive any Subscription requests for this topic.
     */
    public function onSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request)
    {
        //this will broadcast the message to ALL subscribers of this topic.
        $topic->broadcast(['msg' => $connection->resourceId.' has joined push channel'.$topic->getId()]);
    }

    /**
     * This will receive any UnSubscription requests for this topic.
     */
    public function onUnSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request)
    {
        //this will broadcast the message to ALL subscribers of this topic.
        $topic->broadcast(['msg' => $connection->resourceId.' has left '.$topic->getId()]);
    }

    /**
     * This will receive any Publish requests for this topic.
     *
     * @param $event
     *
     * @return mixed|void
     */
    public function onPublish(ConnectionInterface $connection, Topic $topic, WampRequest $request, $event, array $exclude, array $eligible)
    {
        /*
        $topic->getId() will contain the FULL requested uri, so you can proceed based on that

        if ($topic->getId() === 'acme/channel/shout')
        //shout something to all subs.
        */
        if (!\is_string($event)) {
            return;
        }
        $event = json_decode($event, true);
        $topic->broadcast($event);
    }

    /**
     * @param array|string $data
     * @param string       $provider The name of pusher who push the data
     */
    public function onPush(Topic $topic, WampRequest $request, $data, $provider)
    {
        $topic->broadcast($data);
    }

    /**
     * Like RPC is will use to prefix the channel.
     *
     * @return string
     */
    public function getName()
    {
        return 'monithome.push.topic';
    }
}
