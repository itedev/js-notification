<?php


namespace ITE\Js\Notification;

/**
 * Interface NotificatorInterface
 *
 * @author sam0delkin <t.samodelkin@gmail.com>
 */
interface NotificatorInterface
{
    /**
     * @param $title
     * @param $message
     * @param $type
     * @param array $pluginOptions
     * @return mixed
     */
    public function addNotification($title, $message, $type, $pluginOptions = []);

    /**
     * @return Notification[][]|Notification[]
     */
    public function getNotifications();
} 