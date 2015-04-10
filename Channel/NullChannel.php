<?php

namespace ITE\Js\Notification\Channel;

/**
 * Class NullChannel
 *
 * @author  sam0delkin <t.samodelkin@gmail.com>
 */
class NullChannel extends AbstractChannel
{
    /**
     * {@inheritdoc}
     */
    public function getNotifications()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'null';
    }

    /**
     * {@inheritdoc}
     */
    public function getCdnName()
    {
        return 'null';
    }

}