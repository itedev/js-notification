<?php

namespace ITE\Js\Notification;

use ITE\Js\Notification\Channel\ChannelInterface;
use ITE\Js\Notification\Channel\NotificationAliasTrait;
use ITE\Js\Notification\Collector\CollectorInterface;

/**
 * Class Notifier
 *
 * @author  sam0delkin <t.samodelkin@gmail.com>
 */
class Notifier implements NotifierInterface
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
     * @param ChannelInterface $channel
     */
    public function addChannel(ChannelInterface $channel)
    {
        $this->channels[$channel->getName()] = $channel;
    }

    /**
     * @param CollectorInterface $collector
     */
    public function addCollector(CollectorInterface $collector)
    {
        $this->collectors[] = $collector;
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
            throw new \InvalidArgumentException(sprintf('Notification channel "%s" is not defined.', $name));
        }

        $this->currentChannel = $name;

        return $this;
    }

    /**
     * @return ChannelInterface[]
     */
    public function getChannels()
    {
        return $this->channels;
    }

    /**
     * @return Notification[][]
     */
    public function getNotifications()
    {
        $notifications = $this->collectNotifications();
        foreach ($this->channels as $channel) {
            $channelName = $channel->getName();
            if (!array_key_exists($channelName, $notifications)) {
                $notifications[$channelName] = [];
            }

            $notifications[$channelName] = array_merge($notifications[$channelName], $channel->getNotifications());
        }


        return $notifications;
    }

    /**
     * @param string $type
     * @param string $message
     * @param string $title
     * @param array $options
     */
    public function addNotification($type, $message, $title = null, $options = [])
    {
        if (!isset($this->channels[$this->currentChannel])) {
            throw new \InvalidArgumentException(
                sprintf('Notification channel "%s" is not defined.', $this->currentChannel)
            );
        }

        $this->channels[$this->currentChannel]->addNotification($type, $message, $title, $options);
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
            $channelName = $collector->getChannel();
            if (!array_key_exists($channelName, $notifications)) {
                $notifications[$channelName] = [];
            }

            $notifications[$channelName] = array_merge($notifications[$channelName], $collector->collect());
        }

        return $notifications;
    }
}