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
    private $message;

    /**
     * @var string
     */
    private $title;

    /**
     * @var array
     */
    private $options;

    /**
     * @param string $channel
     * @param string $type
     * @param string $message
     * @param string $title
     * @param array  $options
     */
    public function __construct($channel, $type, $message, $title, array $options = [])
    {
        $this->channel = $channel;
        $this->type    = $type;
        $this->message = $message;
        $this->title   = $title;
        $this->options = $options;
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
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'channel' => $this->channel,
            'type'    => $this->type,
            'message' => $this->message,
            'title'   => $this->title,
            'options' => $this->options
        ];
    }
}