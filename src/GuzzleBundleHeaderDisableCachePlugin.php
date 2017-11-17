<?php

namespace Neirda24\Bundle\GuzzleBundleHeaderDisableCachePlugin;

use EightPoints\Bundle\GuzzleBundle\EightPointsGuzzleBundlePlugin;
use Neirda24\Bundle\GuzzleBundleHeaderDisableCachePlugin\EventListener\NoCacheSubscriber;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class GuzzleBundleHeaderDisableCachePlugin extends Bundle implements EightPointsGuzzleBundlePlugin
{
    /**
     * {@inheritdoc}
     */
    public function getPluginName(): string
    {
        return 'header_disable_cache';
    }

    /**
     * {@inheritdoc}
     */
    public function addConfiguration(ArrayNodeDefinition $pluginNode)
    {
        $pluginNode
            ->canBeEnabled()
            ->validate()
                ->ifTrue(function (array $config) {
                    return true === $config['enabled'] && '' === trim($config['header']);
                })
            ->thenInvalid('header is required.')
            ->end()
            ->children()
                ->scalarNode('header')
                    ->defaultValue(NoCacheSubscriber::DEFAULT_SKIP_CACHE_HEADER)
                ->end()
            ->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/Resources/config'));
        $loader->load('subscribers.yml');
    }

    /**
     * {@inheritdoc}
     */
    public function loadForClient(array $config, ContainerBuilder $container, string $clientName, Definition $handler)
    {
        if (true === $config['enabled']) {
            $subscriberDefinition = $container->getDefinition('pichet.cache.no_cache_subscriber');
            $subscriberDefinition->addMethodCall('addGuzzleClient', [
                new Reference(sprintf('eight_points_guzzle.client.%s', $clientName)),
                $config['header']
            ]);
        }
    }
}
