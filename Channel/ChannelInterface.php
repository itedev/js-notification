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
     * @param string $title
     * @param string $message
     * @param string $type
     * @param array  $pluginOptions
     *
     * @return mixed
     */
    public function addNotification($title, $message, $type, $pluginOptions = []);

    /**
     * Return array of all saved notifications.
     *
     * @return Notification[]
     */
    public function getNotifications();

    /**
     * @return string
     */
    public function getName();
}