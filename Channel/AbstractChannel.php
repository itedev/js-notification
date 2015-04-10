<?php

namespace ITE\Js\Notification\Channel;

use ITE\Js\Notification\Notification;
use ITE\JsBundle\SF\PluginTrait;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class AbstractChannel
 *
 * @package ITE\Js\Notification\Channel
 * @author  sam0delkin <t.samodelkin@gmail.com>
 */
abstract class AbstractChannel implements ChannelInterface
{
    use PluginTrait;
    use NotificationAliasTrait;

    /**
     * @var Notification[]
     */
    protected $notifications = [];

    /**
     * @inheritdoc
     */
    public function addNotification($title, $message, $type, $pluginOptions = [])
    {
        $this->notifications [] = new Notification($this->getName(), $type, $title, $message, $pluginOptions);
    }

    /**
     * @inheritdoc
     */
    public function getNotifications()
    {
        return $this->notifications;
    }
}