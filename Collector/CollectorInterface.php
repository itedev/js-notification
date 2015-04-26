<?php

namespace ITE\Js\Notification\Collector;

use ITE\Js\Notification\Notification;

/**
 * Interface CollectorInterface
 *
 * @author  sam0delkin <t.samodelkin@gmail.com>
 */
interface CollectorInterface
{
    /**
     * @return Notification[]
     */
    public function collect();

    /**
     * @return string
     */
    public function getChannel();
}