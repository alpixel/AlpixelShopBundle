<?php

namespace Alpixel\Bundle\ShopBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Tests\Functional\WebTestCase;

/**
 * @author Benjamin HUBERT <benjamin@alpixel.fr>
 */
class BaseTestCase extends WebTestCase
{
    protected static function createKernel(array $options = [])
    {
        return new AppKernel(
            isset($options['config']) ? $options['config'] : 'config.yml'
        );
    }
}
