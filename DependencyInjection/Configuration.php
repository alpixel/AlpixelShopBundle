<?php

namespace Alpixel\Bundle\ShopBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('alpixel_shop');

        $rootNode
            ->children()
                ->arrayNode('authorized_roles')
                    ->treatNullLike([])
                    ->prototype('scalar')->end()
                    ->defaultValue(['ROLE_SUPER_ADMIN'])
                ->end()
                ->scalarNode('customer_class')
                    ->isRequired()
                    ->defaultValue('Alpixel\Bundle\ShopBundle\Entity\CustomerClass')
                ->end()
                ->scalarNode('default_currency')
                    ->defaultValue('EUR')
                ->end()
                ->booleanNode('use_google_analytics')
                    ->defaultValue(false)
                ->end()
                ->arrayNode('stock')
                    ->children()
                        ->scalarNode('strategy')
                            ->isRequired()
                            ->info('You can configure the behaviour of stock management by set with options:\n
                                "soft" (Allow the order, even if the product has not enough stock)\n
                                "tolerant" (Allow the order if the current stock of the product > 0)\n
                                "strict" (Allow the order only if the stock has enough quantities)\n')
                            ->defaultValue('strict')
                            ->validate()
                            ->ifNotInArray(['soft', 'tolerant', 'strict'])
                                ->thenInvalid('Invalid strategy "%s" availables configuration "soft", "tolerant", "strict"')
                            ->end()
                        ->end()
                        ->booleanNode('update')
                            ->isRequired()
                            ->info('The quantity of stock is updated')
                            ->defaultValue(true)
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('product_inheritance')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('key')->end()
                            ->scalarNode('class')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
