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
     * @var array
     */
    protected $cdn;

    /**
     * @var Notification[]
     */
    protected $notifications = [];

    /**
     * @param array $cdn
     */
    public function setCdn(array $cdn)
    {
        $this->cdn = $cdn;
    }

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

    /**
     * @return bool
     */
    public function hasCdn()
    {
        return null !== $this->getCdnName();
    }

    /**
     * @return bool
     */
    public function isCdnEnabled()
    {
        return $this->cdn && $this->cdn['enabled'];
    }

    /**
     * @return string|null
     */
    public function getCdnVersion()
    {
        return $this->cdn ? $this->cdn['version'] : $this->getDefaultCdnVersion();
    }

    /**
     * @return string|null
     */
    public function getCdnName()
    {
        return null;
    }

    /**
     * @return string|null
     */
    public function getDefaultCdnVersion()
    {
        return null;
    }
}