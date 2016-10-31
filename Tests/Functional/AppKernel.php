<?php

namespace Alpixel\Bundle\ShopBundle\Tests\Functional;

use Happyr\GoogleAnalyticsBundle\HappyrGoogleAnalyticsBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Kernel;

/**
 * @author Benjamin HUBERT <benjamin@alpixel.fr>
 */
class AppKernel extends Kernel
{
    private $config;

    public function __construct($config)
    {
        parent::__construct('test', true);

        $fs = new Filesystem();
        if (!$fs->isAbsolutePath($config)) {
            $config = __DIR__.'/config/'.$config;
        }

        if (!file_exists($config)) {
            throw new RuntimeException(sprintf('The config file "%s" does not exist.', $config));
        }

        $this->config = $config;
    }

    public function registerBundles()
    {
        return [
            //Symfony
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),

            //Doctrine
            new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new \Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),

            //ALPIXEL User
            new \Alpixel\Bundle\UserBundle\AlpixelUserBundle(),
            new \FOS\UserBundle\FOSUserBundle(),

            //ALPIXEL Shop
            new \Alpixel\Bundle\ShopBundle\AlpixelShopBundle(),

            new \Alpixel\Bundle\ShopBundle\Tests\Functional\Fixture\Bundle\TestBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->config);
    }

    public function getCacheDir()
    {
        return sys_get_temp_dir().'/TestBundle';
    }

    public function serialize()
    {
        return $this->config;
    }

    public function unserialize($config)
    {
        $this->__construct($config);
    }
}
