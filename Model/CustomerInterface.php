<?php

namespace Alpixel\Bundle\ShopBundle\Model;

use Alpixel\Bundle\ShopBundle\Entity\ProductPrice;

/**
 * Customer.
 */
interface CustomerInterface
{
    /**
     * Add currency.
     *
     * @param \Alpixel\Bundle\ShopBundle\Entity\Currency $currency
     *
     * @return ProductPrice
     */
    public function setCurrency(\Alpixel\Bundle\ShopBundle\Entity\Currency $currency);

    /**
     * Get currency.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCurrency();

    /**
     * Gets the value of firstname.
     *
     * @return string
     */
    public function getFirstname();

    /**
     * Gets the Todo Champ par défaut du nom et prénom de l'utilisateur appliquer les modification sur firstname lors de la V2 du client.
     *
     * @return string
     */
    public function getLastname();

    /**
     * Sets the Todo Champ par défaut du nom et prénom de l'utilisateur appliquer les modification sur firstname lors de la V2 du client.
     *
     * @param string $lastname the lastname
     *
     * @return self
     */
    public function setLastname($lastname);

    /**
     * Sets the value of firstname.
     *
     * @param string $firstname the firstname
     *
     * @return self
     */
    public function setFirstname($firstname);

    /**
     * Returns the user unique id.
     *
     * @return mixed
     */
    public function getId();
}