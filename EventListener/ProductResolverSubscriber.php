<?php

namespace Alpixel\Bundle\ShopBundle\EventListener;

use Alpixel\Bundle\ShopBundle\Entity\Product;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;

class ProductResolverSubscriber implements EventSubscriber
{
    private $productInheritance;

    public function __construct($productInheritance = [])
    {
        $this->productInheritance = $productInheritance;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::loadClassMetadata,
        ];
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        if (empty($this->productInheritance)) {
            return;
        }

        $metadata = $eventArgs->getClassMetadata();
        if (Product::class !== $metadata->getName()) {
            return;
        }

        $discriminatorMap = [];
        foreach ($this->productInheritance as $inheritance) {
            $discriminatorMap[$inheritance['key']] = $inheritance['class'];
        }

        if (!empty($discriminatorMap)) {
            $metadata->setDiscriminatorMap($discriminatorMap);
        }
    }
}
