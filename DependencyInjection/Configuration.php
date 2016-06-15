<?php

namespace Alpixel\Bundle\ShopBundle\DependencyInjection;

use Alpixel\Bundle\ShopBundle\Model\CartInterface;
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
                ->arrayNode('security')
                    ->isRequired()
                    ->children()
                        ->arrayNode('cart_access')
                            ->isRequired()
                            ->children()
                                ->arrayNode('roles')
                                    ->cannotBeEmpty()
                                    ->prototype('scalar')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('customer_class')
                    ->isRequired()
                    ->defaultValue('Alpixel\Bundle\ShopBundle\Entity\Customer')
                ->end()
                ->scalarNode('default_currency')
                    ->defaultValue('EUR')
                ->end()
                ->arrayNode('cart')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('class')
                            ->defaultValue('Alpixel\Bundle\ShopBundle\Entity\Cart')
                            ->validate()
                            ->always(function ($entity) {
                                if (!is_string($entity)) {
                                    throw new \InvalidArgumentException('The "class" parameter must be a string');
                                } else if (!class_exists($entity)) {
                                    throw new \LogicException(sprintf(
                                        'Unable to find the "%s" class, make sure of typo',
                                        $entity
                                    ));
                                } else if (!in_array(CartInterface::class, class_implements($entity))) {
                                    throw new \LogicException(sprintf(
                                        'The "%s" class, must implement the "%s" interface',
                                        $entity,
                                        CartInterface::class
                                    ));
                                }

                                return $entity;
                            })
                            ->end()
                        ->end()
                    ->end()
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
