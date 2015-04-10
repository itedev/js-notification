<?php

namespace ITE\Js\Notification;

use ITE\Common\CdnJs\Resource\Reference;
use ITE\Common\Extension\ExtensionFinder;
use ITE\Js\Notification\Channel\ChannelInterface;
use ITE\Js\Notification\Definition\Builder\PluginDefinition;
use ITE\JsBundle\SF\SFExtension;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference as DIReference;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * Class NotificationExtension
 *
 * @author  sam0delkin <t.samodelkin@gmail.com>
 */
class NotificationExtension extends SFExtension
{
    /**
     * @var NotificationManager
     */
    protected $nm;

    /**
     * @var bool
     */
    private $debug;

    /**
     * @param NotificationManager $nm
     * @param  bool               $debug
     */
    public function __construct(NotificationManager $nm, $debug)
    {
        $this->nm    = $nm;
        $this->debug = $debug;
    }

    /**
     * @inheritdoc
     */
    public function loadConfiguration(array $config, ContainerBuilder $container)
    {
        if ($config['extensions']['notifications']['enabled']) {
            $container->setParameter(
                'ite_js.notifications.default_channel',
                $config['extensions']['notifications']['default_channel']
            );
            $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/Resources/config'));
            $loader->load('services.yml');

            if ($config['extensions']['notifications']['collectors']['session']['enabled']) {
                $container->setParameter(
                    'ite_js.notifications.collectors.session.bag_name',
                    $config['extensions']['notifications']['collectors']['session']['bag_name']
                );
                $loader->load('collector/session.yml');
            }

            $iteDir = __DIR__.'/../../../../';

            ExtensionFinder::loadExtensions(
                function (ChannelInterface $channel) use ($config, $container) {
                    $channel->loadConfiguration($config, $container);
                },
                $iteDir,
                'ITE\Js\Notification\Channel\ChannelInterface',
                __DIR__
            );

            $definition = $container->getDefinition('ite_js.notifications.manager');

            if (!$definition) {
                return;
            }

            $services = $container->findTaggedServiceIds('ite_js.notifiactions.collector');

            foreach ($services as $id => $attributes) {
                $definition->addMethodCall('addCollector', [new DIReference($id)]);
            }

            $services = $container->findTaggedServiceIds('ite_js.notification.channel');

            foreach ($services as $id => $attributes) {
                $definition->addMethodCall('addChannel', [new DIReference($id)]);
            }

        }
    }

    /**
     * @inheritdoc
     */
    public function getConfiguration(ContainerBuilder $container)
    {
        $node = new TreeBuilder();

        $node         = $node->root('notifications');
        $channelsNode = $node
            ->canBeEnabled()
            ->children()
            ->scalarNode('default_channel')->defaultValue('null')->end()
            ->arrayNode('channels')
            ->children();;

        $iteDir = __DIR__.'/../../../../';

        ExtensionFinder::loadExtensions(
            function (ChannelInterface $channel) use ($channelsNode, $container) {
                $config = $channel->getConfiguration($container);
                if ($config) {
                    $channelsNode->append($config);
                }
            },
            $iteDir,
            'ITE\Js\Notification\Channel\ChannelInterface',
            __DIR__
        );

        $node->children()
            ->arrayNode('collectors')
            ->addDefaultsIfNotSet()
            ->children()
            ->arrayNode('session')
            ->canBeEnabled()
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('bag_name')
            ->defaultValue('flashes')
            ->info('Session FlashBag name.')
            ->end()
            ->scalarNode('channel_name')
            ->defaultValue('null')
            ->info('Channel name which need to use with collector.')
            ->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end();

        return $node;
    }

    /**
     * @inheritdoc
     */
    public function onAjaxResponse(FilterResponseEvent $event)
    {
        $notifications = $this->nm->getNotifications();

        if (!empty($notifications)) {
            $arrayNotifications = [];
            foreach ($notifications as $channel => $nn) {
                $arrayNotifications[$channel] = [];
                foreach ($nn as $n) {
                    $arrayNotifications[$channel] [] = $n->toArray();
                }

            }

            $event->getResponse()->headers->add(['X-SF-Notifications' => json_encode($arrayNotifications)]);
        }
    }

    /**
     * @inheritdoc
     */
    public function getJavascripts()
    {
        $js = [__DIR__.'/Resources/public/js/sf.notification.js'];

        foreach ($this->nm->getChannels() as $channel) {
            $js = array_merge($js, $channel->getJavascripts());
        }

        return $js;
    }

    /**
     * @inheritdoc
     */
    public function getInlineJavascripts()
    {
        $notifications = $this->nm->getNotifications();

        if (empty($notifications)) {
            return '';
        }

        $dump = '</script>'.$this->dumpCDN().'<script>';
        $dump .= '(function($){$(function(){';

        foreach ($notifications as $channel => $ns) {
            foreach ($ns as $notification) {
                $dump .= 'SF.flashes.addObject('.json_encode($notification->toArray()).');';
            }
        }

        $dump .= 'SF.flashes.show();';
        $dump .= '});})(jQuery);';

        return $dump;
    }

    /**
     * @return string
     */
    protected function dumpCDN()
    {
        $cdnAssets = '';

        foreach ($this->nm->getChannels() as $channel) {
            $references = $channel->getCdnJavascripts($this->debug);
            foreach ($references as $reference) {
                if (!($reference instanceof Reference)) {
                    throw new \InvalidArgumentException(
                        'getCdnJavascripts method should return array of Reference class.'
                    );
                }

                $cdnAssets .= sprintf('<script type="text/javascript" src="%s"></script>', $reference->getUrl());
            }

            $references = $channel->getCdnStylesheets($this->debug);
            foreach ($references as $reference) {
                if (!($reference instanceof Reference)) {
                    throw new \InvalidArgumentException(
                        'getCdnStylesheets method should return array of Reference class.'
                    );
                }

                $cdnAssets .= sprintf('<link rel="stylesheet" href="%s" />', $reference->getUrl());
            }
        }

        return $cdnAssets;
    }

}