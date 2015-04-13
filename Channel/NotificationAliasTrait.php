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
     * @param $title
     * @param $message
     */
    public function success($title, $message)
    {
        $this->addNotification($title, $message, Notification::TYPE_SUCCESS);
    }

    /**
     * @param $title
     * @param $message
     */
    public function info($title, $message)
    {
        $this->addNotification($title, $message, Notification::TYPE_INFO);
    }

    /**
     * @param $title
     * @param $message
     */
    public function warning($title, $message)
    {
        $this->addNotification($title, $message, Notification::TYPE_WARNING);
    }

    /**
     * @param $title
     * @param $message
     */
    public function error($title, $message)
    {
        $this->addNotification($title, $message, Notification::TYPE_ERROR);
    }
}