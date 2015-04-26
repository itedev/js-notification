<?php

namespace ITE\Js\Notification\Channel;

use ITE\Js\Notification\Notification;

/**
 * Class NotificationAliasTrait
 *
 * @author  sam0delkin <t.samodelkin@gmail.com>
 */
trait NotificationAliasTrait
{
    /**
     * @param string $message
     * @param string $title
     */
    public function success($message, $title)
    {
        $this->addNotification(Notification::TYPE_SUCCESS, $message, $title);
    }

    /**
     * @param string $message
     * @param string $title
     */
    public function info($message, $title)
    {
        $this->addNotification(Notification::TYPE_INFO, $message, $title);
    }

    /**
     * @param string $message
     * @param string $title
     */
    public function warning($message, $title)
    {
        $this->addNotification(Notification::TYPE_WARNING, $message, $title);
    }

    /**
     * @param string $message
     * @param string $title
     */
    public function error($message, $title)
    {
        $this->addNotification(Notification::TYPE_ERROR, $message, $title);
    }
}