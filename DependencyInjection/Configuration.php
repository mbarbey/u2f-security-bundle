<?php

namespace Mbarbey\U2fSecurityBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('mbarbey_u2f_security');

        $rootNode
            ->children()
                ->scalarNode('authentication_route')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->arrayNode('whitelist_routes')->scalarPrototype()->end()
            ->end()
        ;

        return $treeBuilder;
    }
}