# AlpixelShopBundle
:sweat_smile: Things are getting serious.


## Installation

### Download the bundle

From your project directory.
```
$ composer require alpixel/shopbundle
```

### Enable the bundle
Then, you need to enable the bundle by adding the following line in the ```app/AppKernel.php``` file.
```
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Alpixel\Bundle\ShopBundle\AlpixelShopBundle(),
        );

        // ...
    }
}
```
### Configuration

```
alpixel_shop:
    customer_class: AppBundle\Entity\CustomEntity # A custom class extend the Alpixel\Bundle\ShopBundle\Entity\Customer class
    stock:
        strategy: soft #['soft', 'tolerant', 'strict']
        update: false # Update stock quantity for products [true, false]
    product_inheritance:
            - { key: productEskenazi, class: AppBundle\Entity\CustomProduct }
```

The bundle is now ready to work

## Configuration Reference

WIP
