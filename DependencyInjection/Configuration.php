<?php

namespace Ecommerce\Bundle\CatalogBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Philipp Wahala <philipp.wahala@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree.
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $treeBuilder->root('ecommerce_catalog')
            ->children()
                ->arrayNode('persistence')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('phpcr')
                            ->addDefaultsIfNotSet()
                            ->canBeDisabled()
                            ->children()
                                ->scalarNode('manager_name')->defaultNull()->end()
                                ->scalarNode('product_basepath')->defaultValue('/ecommerce/product')->end()
                                ->scalarNode('product_class')->defaultValue('Ecommerce\Bundle\CatalogBundle\Doctrine\Phpcr\Product')->end()
                            ->end()
                        ->end()
                        ->arrayNode('orm')
                            ->addDefaultsIfNotSet()
                            ->canBeDisabled()
                            ->children()
                                ->scalarNode('manager_name')->defaultNull()->end()
                                ->scalarNode('product_reference_class')->defaultValue('Ecommerce\Bundle\CatalogBundle\Doctrine\Orm\ProductReference')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('property_files')
                    ->prototype('scalar')->end()
                ->end()

                ->arrayNode('type_files')
                    ->prototype('scalar')->end()
                ->end()

                ->scalarNode('default_template')->end()

                ->enumNode('use_jms_serializer')
                    ->values(array(true, false, 'auto'))
                    ->defaultValue('auto')
                ->end()
            ->end()
        ;


        return $treeBuilder;
    }
}
