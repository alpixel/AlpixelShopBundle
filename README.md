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
            
            new Happyr\GoogleAnalyticsBundle\HappyrGoogleAnalyticsBundle(), 
            new Http\HttplugBundle\HttplugBundle(),
            //If you don't disable the analytics trackings, you will need the bundle above
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
            - { key: myCustomKey, class: AppBundle\Entity\CustomProduct }
    use_google_analytics: true #Allow google analytics trackings with happyr bundle
```

The bundle is now ready to work

## Configuration Reference

```
alpixel_shop:
    customer_class: AppBundle\Entity\CustomEntity
```
You can add a custom entity who extend the Alpixel\Bundle\ShopBundle\Entity\Customer to add your properties or also directly use the Alpixel\Bundle\ShopBundle\Entity\Customer entity.

```
alpixel_shop:
    stock:
        strategy: soft #['soft', 'tolerant', 'strict']
```
The option stock strategy allow you to work with different behaviour of stock management.

soft: Allow the order, even if the product has not enough stock
tolerant: Allow the order if the current stock of the product > 0
strict: Allow the order only if the stock has enough quantities

