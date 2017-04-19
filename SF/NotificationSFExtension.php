<?php

namespace ITE\Js\Notification\SF;

use ITE\Common\Extension\ExtensionFinder;
use ITE\Js\Notification\Channel\ChannelInterface;
use ITE\Js\Notification\Notifier;
use ITE\JsBundle\EventListener\Event\AjaxResponseEvent;
use ITE\JsBundle\SF\SFExtension;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class NotificationSFExtension
 *
 * @author  sam0delkin <t.samodelkin@gmail.com>
 */
class NotificationSFExtension extends SFExtension
{
    /**
     * @var Notifier
     */
    protected $notifier;

    /**
     * @var bool
     */
    private $debug;

    /**
     * @param Notifier $notifier
     * @param  bool $debug
     */
    public function __construct(Notifier $notifier, $debug)
    {
        $this->notifier = $notifier;
        $this->debug = $debug;
    }

    /**
     * @inheritdoc
     */
    public function getConfiguration(ContainerBuilder $container)
    {
        $node = new TreeBuilder();

        $node = $node->root('notification');
        /** @var ArrayNodeDefinition $channelsNode */
        $channelsNode = $node
            ->canBeEnabled()
            ->children()
                ->scalarNode('default_channel')
                    ->defaultValue('null')
                ->end()
                ->arrayNode('channels')
                    ->children();

        $iteDir = __DIR__.'/../../../../../';

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

        $node
            ->children()
                ->arrayNode('collectors')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('session')
                            ->canBeEnabled()
                            ->children()
                                ->scalarNode('channel_name')
                                    ->defaultValue('null')
                                    ->info('Channel name which need to use with collector.')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $node;
    }

    /**
     * @inheritdoc
     */
    public function loadConfiguration(array $config, ContainerBuilder $container)
    {
        if ($config['extensions']['notification']['enabled']) {
            $container->setParameter(
                'ite_js.notification.default_channel',
                $config['extensions']['notification']['default_channel']
            );
            $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
            $loader->load('sf.notification.yml');

            if ($config['extensions']['notification']['collectors']['session']['enabled']) {
                $container->setParameter(
                    'ite_js.notification.collectors.session.bag_name',
                    $config['extensions']['notification']['collectors']['session']['bag_name']
                );
                $loader->load('collector/session.yml');
            }

            $iteDir = __DIR__.'/../../../../../';

            ExtensionFinder::loadExtensions(
                function (ChannelInterface $channel) use ($config, $container) {
                    $channel->loadConfiguration($config, $container);
                },
                $iteDir,
                'ITE\Js\Notification\Channel\ChannelInterface',
                __DIR__
            );

            if (!$container->hasDefinition('ite_js.notification.notifier')) {
                return;
            }

            $definition = $container->getDefinition('ite_js.notification.notifier');

            $taggedServices = $container->findTaggedServiceIds('ite_js.notification.collector');
            foreach ($taggedServices as $id => $tagAttributes) {
                $definition->addMethodCall('addCollector', [new Reference($id)]);
            }

            $taggedServices = $container->findTaggedServiceIds('ite_js.notification.channel');
            foreach ($taggedServices as $id => $tagAttributes) {
                $definition->addMethodCall('addChannel', [new Reference($id)]);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function onAjaxResponse(AjaxResponseEvent $event)
    {
        $notifications = $this->notifier->getNotifications();
        if (!empty($notifications)) {
            $arrayNotifications = [];
            foreach ($notifications as $channel => $nn) {
                $arrayNotifications[$channel] = [];
                foreach ($nn as $n) {
                    $arrayNotifications[$channel][] = $n->toArray();
                }
            }

            $event->getAjaxDataBag()->addHeaderData('notifications', $arrayNotifications);
        }
    }

    /**
     * @inheritdoc
     */
    public function getJavascripts()
    {
        $js = [__DIR__.'/../Resources/public/js/sf.notification.js'];

        foreach ($this->notifier->getChannels() as $channel) {
            $js = array_merge($js, $channel->getJavascripts());
        }

        return $js;
    }

    /**
     * @inheritdoc
     */
    public function dump()
    {
        $notifications = $this->notifier->getNotifications();
        if (empty($notifications)) {
            return '';
        }

        $dump = '(function($){$(function(){';
        foreach ($notifications as $channel => $ns) {
            foreach ($ns as $notification) {
                $dump .= 'SF.flashes.addObject(' . json_encode($notification->toArray()) . ');';
            }
        }
        $dump .= 'SF.flashes.show();';
        $dump .= '});})(jQuery);';

        return $dump;
    }

    /**
     * {@inheritdoc}
     */
    public function getCdnStylesheets($debug)
    {
        $stylesheets = [];
        foreach ($this->notifier->getChannels() as $channel) {
            $stylesheets = array_merge($stylesheets, $channel->getCdnStylesheets($debug));
        }

        return $stylesheets;
    }

    /**
     * {@inheritdoc}
     */
    public function getCdnJavascripts($debug)
    {
        $javascripts = [];
        foreach ($this->notifier->getChannels() as $channel) {
            $javascripts = array_merge($javascripts, $channel->getCdnJavascripts($debug));
        }

        return $javascripts;
    }
}
