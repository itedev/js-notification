<?php

namespace ITE\Js\Notification\Collector;

/**
 * Interface CollectorInterface
 *
 * @package ITE\Js\Notification\Collector
 * @author  sam0delkin <t.samodelkin@gmail.com>
 */
interface CollectorInterface
{
    /**
     * @return []
     */
    public function collect();
}