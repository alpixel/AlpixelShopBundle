<?php

namespace Alpixel\Bundle\ShopBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class AlpixelShopExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('alpixel_shop.stock', $config['stock']);
        $container->setParameter('alpixel_shop.customer_class', $config['customer_class']);
        $container->setParameter('alpixel_shop.default_currency', $config['default_currency']);
        $container->setParameter(
            'alpixel_shop.product_inheritance',
            $config['product_inheritance']
        );

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        if ($config['use_google_analytics'] === true) {
            $container
                ->setDefinition(
                    'alpixel_shop.subscriber.analytics',
                    new Definition(
                        'Alpixel\Bundle\ShopBundle\EventListener\AnalyticsSubscriber',
                        [
                            new Reference('security.token_storage'),
                            new Reference('happyr.google_analytics.tracker'),
                        ]
                    )
                )
                ->addTag('kernel.event_subscriber');
        }
    }
}
