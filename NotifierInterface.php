<?php

namespace ITE\Js\Notification;

/**
 * Interface NotifierInterface
 *
 * @author sam0delkin <t.samodelkin@gmail.com>
 */
interface NotifierInterface
{
    /**
     * @param $title
     * @param $message
     * @param $type
     * @param array $pluginOptions
     */
    public function addNotification($title, $message, $type, $pluginOptions = []);

    /**
     * @return Notification[][]
     */
    public function getNotifications();
} 