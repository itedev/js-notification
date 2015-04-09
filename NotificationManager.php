<?php

namespace ITE\Js\Notification;

use ITE\Js\Notification\Collector\CollectorInterface;

/**
 * Class NotificationManager
 *
 * @package ITE\Js\Notification
 * @author  sam0delkin <t.samodelkin@gmail.com>
 */
class NotificationManager
{
    /**
     * @var array
     */
    protected $notifications = [];

    /**
     * @var CollectorInterface[]
     */
    protected $collectors = [];

    /**
     * @return array
     */
    public function getNotifications()
    {
        $this->collectNotifications();

        return $this->notifications;
    }

    /**
     * @param       $title
     * @param       $message
     * @param       $type
     * @param array $pluginOptions
     */
    public function addNotification($title, $message, $type, $pluginOptions = [])
    {
        $this->notifications [] = [
            'title'         => $title,
            'message'       => $message,
            'type'          => $type,
            'pluginOptions' => $pluginOptions
        ];
    }

    /**
     * @param CollectorInterface $collector
     */
    public function addCollector(CollectorInterface $collector)
    {
        $this->collectors []= $collector;
    }

    /**
     * @param $title
     * @param $message
     */
    public function success($title, $message)
    {
        $this->addNotification($title, $message, 'success');
    }

    /**
     * @param $title
     * @param $message
     */
    public function info($title, $message)
    {
        $this->addNotification($title, $message, 'info');
    }

    /**
     * @param $title
     * @param $message
     */
    public function warning($title, $message)
    {
        $this->addNotification($title, $message, 'warning');
    }

    /**
     * @param $title
     * @param $message
     */
    public function error($title, $message)
    {
        $this->addNotification($title, $message, 'error');
    }

    /**
     *
     */
    protected function collectNotifications()
    {
        foreach ($this->collectors as $collector) {
            array_merge($this->notifications, $collector->collect());
        }
    }
}