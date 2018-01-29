<?php

namespace Alpixel\Bundle\ShopBundle\Repository\Shipment;

use Alpixel\Bundle\ShopBundle\Entity\Currency;
use Alpixel\Bundle\ShopBundle\Entity\Shipment\Carrier;
use Alpixel\Bundle\ShopBundle\Entity\Shipment\Shipment;
use Alpixel\Bundle\ShopBundle\Entity\Shipment\Zone;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;


/**
 * @author Alexis BUSSIERES <alexis@alpixel.fr>
 */
class ShipmentRepository extends EntityRepository
{
    /**
     * @param \Alpixel\Bundle\ShopBundle\Entity\Shipment\Zone $zone
     * @param \Alpixel\Bundle\ShopBundle\Entity\Currency $currency
     * @param \Alpixel\Bundle\ShopBundle\Entity\Shipment\Carrier $carrier
     * @param float $purchase
     * @return \Alpixel\Bundle\ShopBundle\Entity\Shipment\Shipment|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneForCartCustomer(
        Zone $zone,
        Carrier $carrier,
        Currency $currency,
        float $purchase
    )
    {
        $qb = $this->createQueryBuilder('s');

        return $qb
            ->join('s.currency', 'cu')
            ->join('s.carrier', 'ca')
            ->join('s.zone', 'z')
            ->where('cu.id = :currency')
            ->andWhere('ca.id = :carrier')
            ->andWhere('z.id = :zone')
            ->andWhere(self::exprForPurchase($qb))
            ->setParameters([
                'currency' => $currency,
                'carrier' => $carrier,
                'zone' => $zone,
                'purchase' => $purchase,
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Return an Expr\Orx object to query a purchase between minPurchase and maxPurchase
     * or puchase superior to minPurchase and maxPurchase is null
     *
     * @param QueryBuilder $qb
     * @return \Doctrine\ORM\Query\Expr\Orx
     */
    private static function exprForPurchase(QueryBuilder $qb): Expr\Orx
    {

        return $qb->expr()->orX(
            $qb->expr()->andX(
                $qb->expr()->gte(':purchase', 's.minPurchase'),
                $qb->expr()->lte(':purchase', 's.maxPurchase')
            ),
            $qb->expr()->andX(
                $qb->expr()->gte(':purchase', 's.minPurchase'),
                $qb->expr()->isNull('s.maxPurchase')
            )
        );
    }
}
