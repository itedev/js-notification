<?php

namespace ITE\Js\Notification\Channel;

/**
 * Class NullChannel
 *
 * @package ITE\Js\Notification\Channel
 * @author  sam0delkin <t.samodelkin@gmail.com>
 */
class NullChannel extends AbstractChannel
{
    /**
     * @inheritdoc
     */
    public function getNotifications()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'null';
    }
}