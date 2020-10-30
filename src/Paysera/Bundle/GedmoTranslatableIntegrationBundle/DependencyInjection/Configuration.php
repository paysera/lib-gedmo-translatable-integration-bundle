<?php

declare(strict_types=1);

namespace Paysera\Bundle\GedmoTranslatableIntegrationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('paysera_gedmo_translatable_integration');

        $rootNode
            ->children()
                ->scalarNode('default_locale')
                ->isRequired()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
