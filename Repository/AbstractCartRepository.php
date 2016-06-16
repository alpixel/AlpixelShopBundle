<?php

namespace Alpixel\Bundle\ShopBundle\Repository;

use Alpixel\Bundle\ShopBundle\Model\CustomerInterface;
use Doctrine\ORM\EntityRepository;

/**
 * AbstractCartRepository
 */
class AbstractCartRepository extends EntityRepository
{
    public function queryCurrentCartByCustomer(CustomerInterface $customer)
    {
        return $this->createQueryBuilder('c')
            ->where('c.customer = :customer')
            ->andWhere('c.order IS NULL')
            ->setParameter('customer', $customer)
            ->orderBy('c.createdAt', 'DESC')
            ->setMaxResults(1);
    }

    public function findCurrentCartByCustomer(CustomerInterface $customer)
    {
        return $this->queryCurrentCartByCustomer($customer)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
