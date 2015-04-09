<?php

namespace ITE\Js\Notification;

use ITE\JsBundle\SF\SFExtension;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * Class NotificationExtension
 *
 * @package ITE\Js\Notification
 * @author  sam0delkin <t.samodelkin@gmail.com>
 */
class NotificationExtension extends SFExtension
{
    /**
     * @var NotificationManager
     */
    protected $nm;

    public function __construct(NotificationManager $nm)
    {
        $this->nm = $nm;
    }


    public function loadConfiguration(array $config, ContainerBuilder $container)
    {
        if ($config['extensions']['notifications']['enabled']) {
            $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/Resources/config'));
            $loader->load('services.yml');

            if ($config['extensions']['notifications']['collectors']['session']['enabled']) {
                $container->setParameter(
                    'ite_js.notifications.collectors.session.bag_name',
                    $config['extensions']['notifications']['collectors']['session']['bag_name']
                );
                $loader->load('collector/session.yml');
            }

            $definition = $container->getDefinition('ite_js.notifications.manager');

            if (!$definition) {
                return;
            }

            $services = $container->findTaggedServiceIds('ite_js.notifiactions.collector');

            foreach ($services as $id => $attributes) {
                $definition->addMethodCall('addCollector', [new Reference($id)]);
            }

        }
    }

    public function addConfiguration(ArrayNodeDefinition $pluginsNode, ContainerBuilder $container)
    {
        $pluginsNode
            ->children()
                ->arrayNode('notifications')
                    ->canBeEnabled()
                    ->children()
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
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    public function onAjaxResponse(FilterResponseEvent $event)
    {
        $notifications = $this->nm->getNotifications();

        if (!empty($notifications)) {
            $event->getResponse()->headers->add(['X-SF-Notifications' => json_encode($notifications)]);
        }
    }

    public function getJavascripts()
    {
        return [__DIR__ . '/Resources/public/js/sf.flash_bag.js'];
    }


    public function dump()
    {
        $notifications = $this->nm->getNotifications();

        if (empty($notifications)) {
            return '';
        }

        $dump = '';
        $dump .= '(function($){$(function(){';

        foreach ($notifications as $notification) {
            $pluginOptions = json_encode($notification['pluginOptions']);
            unset($notification['pluginOptions']);
            $dump .= 'SF.flashes.add("'.implode('","', $notification).'", '.$pluginOptions.');';
        }

        $dump .= 'SF.flashes.show();';
        $dump .= '});})(jQuery);';

        return $dump;
    }


}