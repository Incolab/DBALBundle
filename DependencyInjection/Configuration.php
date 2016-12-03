<?php

namespace Incolab\DBALBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('incolab_database');
        
        $rootNode
            ->children()
                ->arrayNode('connections')
                    ->prototype('array')
                        ->beforeNormalization()
                            ->ifString()
                                ->then(function($v) {return array("url" => $v);})
                            ->end()
                        ->children()
                            ->scalarNode('url')
                                ->defaultNull()
                            ->end()
                            ->scalarNode('driver')
                                ->defaultNull()
                            ->end()
                            ->scalarNode('user')
                                ->defaultNull()
                            ->end()
                            ->scalarNode('password')
                                ->defaultNull()
                            ->end()
                            ->scalarNode('host')
                                ->defaultNull()
                            ->end()
                            ->scalarNode('dbname')
                                ->defaultNull()
                            ->end()
                            ->scalarNode('path')
                                ->defaultNull()
                            ->end()
                            ->arrayNode('migrations')
                                ->children()
                                    ->scalarNode('namespace')
                                        ->defaultNull()
                                    ->end()
                                    ->scalarNode('table')
                                        ->defaultNull()
                                    ->end()
                                    ->scalarNode('directory')
                                        ->defaultNull()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('default_connection')
                    ->defaultNull()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
