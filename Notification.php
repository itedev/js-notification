<?php

namespace ITE\Js\Notification;

/**
 * Class Notification
 *
 * @author  sam0delkin <t.samodelkin@gmail.com>
 */
class Notification
{
    const TYPE_SUCCESS = 'success';
    const TYPE_INFO = 'info';
    const TYPE_WARNING = 'warning';
    const TYPE_ERROR = 'error';

    /**
     * @var string
     */
    private $channel;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $message;

    /**
     * @var array
     */
    private $pluginOptions;

    /**
     * @param string $channel
     * @param string $type
     * @param string $title
     * @param string $message
     * @param array  $pluginOptions
     */
    function __construct($channel, $type, $title, $message, $pluginOptions = [])
    {
        $this->channel       = $channel;
        $this->type          = $type;
        $this->title         = $title;
        $this->message       = $message;
        $this->pluginOptions = $pluginOptions;
    }

    /**
     * @return string
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return array
     */
    public function getPluginOptions()
    {
        return $this->pluginOptions;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'channel'       => $this->channel,
            'title'         => $this->title,
            'type'          => $this->type,
            'message'       => $this->message,
            'pluginOptions' => $this->pluginOptions
        ];
    }
}