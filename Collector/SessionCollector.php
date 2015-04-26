<?php

namespace ITE\Js\Notification\Collector;

use ITE\Js\Notification\Notification;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class SessionCollector
 *
 * @author  sam0delkin <t.samodelkin@gmail.com>
 */
class SessionCollector implements CollectorInterface
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var string
     */
    protected $channelName;

    /**
     * @param Session $session
     * @param string  $channelName
     */
    public function __construct(Session $session, $channelName = 'null')
    {
        $this->session     = $session;
        $this->channelName = $channelName;
    }

    /**
     * {@inheritdoc}
     */
    public function collect()
    {
        $notifications = [];

        $flashes = $this->session->getFlashBag()->all();
        foreach ($flashes as $type => $messages) {
            foreach ($messages as $message) {
                $notifications[] = new Notification($this->channelName, $type, $message, '');
            }
        }

        return $notifications;
    }

    /**
     * {@inheritdoc}
     */
    public function getChannel()
    {
        return $this->channelName;
    }

}