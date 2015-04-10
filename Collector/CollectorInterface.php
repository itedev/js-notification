<?php

namespace ITE\Js\Notification\Collector;

/**
 * Interface CollectorInterface
 *
 * @author  sam0delkin <t.samodelkin@gmail.com>
 */
interface CollectorInterface
{
    /**
     * @return []
     */
    public function collect();

    /**
     * @return string
     */
    public function getChannel();
}