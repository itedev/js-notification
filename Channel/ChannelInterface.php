<?php

namespace ITE\Js\Notification\Channel;

use ITE\Common\DependencyInjection\ExtensionInterface;
use ITE\Js\Notification\Notification;
use ITE\JsBundle\SF\PluginInterface;

/**
 * Interface ChannelInterface
 *
 * @author  sam0delkin <t.samodelkin@gmail.com>
 */
interface ChannelInterface extends PluginInterface
{
    /**
     * Add notification to channel.
     *
     * @param string $type
     * @param string $message
     * @param string $title
     * @param array  $options
     *
     * @return mixed
     */
    public function addNotification($type, $message, $title, array $options = []);

    /**
     * Return array of all saved notifications.
     *
     * @return array|Notification[]
     */
    public function getNotifications();

    /**
     * @return string
     */
    public function getName();
}