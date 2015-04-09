<?php

namespace ITE\Js\Notification\Collector;

use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class SessionCollector
 *
 * @package ITE\Js\Notification\Collector
 * @author  sam0delkin <t.samodelkin@gmail.com>
 */
class SessionCollector implements CollectorInterface
{
    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var string
     */
    protected $bagName = 'flashes';

    /**
     * @param SessionInterface $session
     * @param                  $bagName
     */
    public function __construct(SessionInterface $session, $bagName)
    {
        $this->session = $session;
        $this->bagName = $bagName;
    }

    /**
     * @inheritdoc
     */
    public function collect()
    {
        $notifications = [];
        $bag = $this->session->getBag($this->bagName);

        if ($bag instanceof FlashBagInterface) {
            $flashes = $bag->all();
            foreach ($flashes as $type => $typeFlashes) {
                foreach ($typeFlashes as $flash) {
                    $notifications []= [
                        'type' => $type,
                        'title' => '',
                        'message' => $flash,
                    ];
                }
            }
        } else {
            throw new \InvalidArgumentException(
                'For collecting flashes, SessionBag should be instance of FlashBagInterface.'
            );
        }

        return $notifications;
    }

}