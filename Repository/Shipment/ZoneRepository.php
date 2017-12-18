<?php

namespace Alpixel\Bundle\ShopBundle\Repository\Shipment;

use Alpixel\Bundle\ShopBundle\Entity\Shipment\Zone;
use Doctrine\ORM\EntityRepository;

/**
 * @author Alexis BUSSIERES <alexis@alpixel.fr>
 */
class ZoneRepository extends EntityRepository
{
    /**
     * @param $idOrName
     * @return \Alpixel\Bundle\ShopBundle\Entity\Shipment\Zone|null
     */
    public function findOneByIdOrName($idOrName)
    {
        $zone = null;
        $criteria = [];

        if (is_int($idOrName)) {
            $criteria['id'] = $idOrName;
        } elseif (is_string($idOrName)) {
            $criteria['name'] = $idOrName;
        } else {
            throw new \UnexpectedValueException('Must be an non empty string or int');
        }

        if (!empty($criteria)) {
            $zone = $this->findOneBy($criteria);
        }

        return $zone;
    }
}
