<?php

namespace ITE\Js\Notification;

use ITE\Js\Notification\Channel\ChannelInterface;
use ITE\Js\Notification\Channel\NotificationAliasTrait;
use ITE\Js\Notification\Collector\CollectorInterface;

/**
 * Class NotificationManager
 *
 * @package ITE\Js\Notification
 * @author  sam0delkin <t.samodelkin@gmail.com>
 */
class NotificationManager
{
    use NotificationAliasTrait;

    /**
     * @var CollectorInterface[]
     */
    protected $collectors = [];

    /**
     * @var ChannelInterface[]
     */
    protected $channels = [];

    /**
     * @var string
     */
    protected $currentChannel = 'null';

    /**
     * @param string $currentChannel
     */
    public function __construct($currentChannel)
    {
        $this->currentChannel = $currentChannel;
    }


    /**
     * Select current channel.
     *
     * @param $name
     * @return $this
     */
    public function channel($name)
    {
        if (!isset($this->channels[$name])) {
            throw new \InvalidArgumentException(sprintf('Notifications channel "%s" is not defined.', $name));
        }

        $this->currentChannel = $name;

        return $this;
    }

    /**
     * @return Notification[][]
     */
    public function getNotifications()
    {
        $notifications = $this->collectNotifications();

        foreach ($this->channels as $channel) {
            $notifications[$channel->getName()] = empty($notifications[$channel->getName()])
                ? [] : $notifications[$channel->getName()];

            $notifications[$channel->getName()] = array_merge(
                $notifications[$channel->getName()],
                $channel->getNotifications() ?: []
            );
        }


        return $notifications;
    }

    /**
     * @return Channel\ChannelInterface[]
     */
    public function getChannels()
    {
        return $this->channels;
    }


    /**
     * @param       $title
     * @param       $message
     * @param       $type
     * @param array $pluginOptions
     */
    public function addNotification($title, $message, $type, $pluginOptions = [])
    {
        if (!isset($this->channels[$this->currentChannel])) {
            throw new \InvalidArgumentException(
                sprintf('Notifications channel "%s" is not defined.', $this->currentChannel)
            );
        }

        $this->channels[$this->currentChannel]->addNotification($title, $message, $type, $pluginOptions);
    }

    /**
     * @param CollectorInterface $collector
     */
    public function addCollector(CollectorInterface $collector)
    {
        $this->collectors [] = $collector;
    }

    /**
     * @param ChannelInterface $channel
     */
    public function addChannel(ChannelInterface $channel)
    {
        $this->channels[$channel->getName()] = $channel;
    }

    /**
     * Collect all notifications from all collectors.
     *
     * @return array
     */
    protected function collectNotifications()
    {
        $notifications = [];

        foreach ($this->collectors as $collector) {
            $notifications[$collector->getChannel()] = empty($notifications[$collector->getChannel()])
                ? [] : $notifications[$collector->getChannel()];

            array_merge($notifications[$collector->getChannel()], $collector->collect());
        }

        return $notifications;
    }
}