<?php

namespace Alpixel\Bundle\ShopBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;

class DynamicRelationSubscriber implements EventSubscriber
{
    protected $customerClass;

    public function __construct($customerClass)
    {
        $this->customerClass = $customerClass;
    }

    /**
     * {@inheritdoc}
     */
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
        // the $metadata is the whole mapping info for this class
        $metadata = $eventArgs->getClassMetadata();

        $classes = [
            'Alpixel\Bundle\ShopBundle\Entity\Cart',
            'Alpixel\Bundle\ShopBundle\Entity\Order',
        ];

        if (!in_array($metadata->getName(), $classes)) {
            return;
        }

        if ($metadata->getName() === 'Alpixel\Bundle\ShopBundle\Entity\Order') {
            $onDelete = 'SET NULL';
            $nullable = true;
        } else {
            $onDelete = 'CASCADE';
            $nullable = false;
        }

        $metadata->mapManyToOne([
            'targetEntity' => $this->customerClass,
            'fieldName'    => 'customer',
            'joinColumns'  => [[
                'name'                 => 'customer_id',
                'referencedColumnName' => 'user_id',
                'nullable'             => $nullable,
                'onDelete'             => $onDelete,
            ]],
        ]);
    }
}
