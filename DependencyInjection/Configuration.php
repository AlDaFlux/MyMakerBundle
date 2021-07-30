<?php

namespace Aldaflux\MyMakerBundle\DependencyInjection;
use Symfony\Component\HttpKernel\Kernel;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
//        $treeBuilder = new TreeBuilder('aldaflux_my_maker');
        $treeBuilder = new TreeBuilder('aldaflux_mymaker');

        if (Kernel::VERSION_ID >= 40200) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            $rootNode = $treeBuilder->root('aldaflux_mymaker');
        }        
        
        $rootNode->children()
        ->arrayNode('admin')
            ->children()
                ->scalarNode('folder')->end()
                ->scalarNode('folder_admin')->end()
            ->end()
        ->end()
    ->end()
;
        return $treeBuilder;
    }


}
