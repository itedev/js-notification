<?php

namespace ITE\Js\Notification\Channel;

use ITE\Js\Notification\Notification;
use ITE\JsBundle\SF\PluginTrait;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class AbstractChannel
 *
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
    public function addNotification($type, $message, $title, array $options = [])
    {
        $this->notifications[] = new Notification($this->getName(), $type, $message, $title, $options);
    }

    /**
     * @inheritdoc
     */
    public function getNotifications()
    {
        return $this->notifications;
    }
}