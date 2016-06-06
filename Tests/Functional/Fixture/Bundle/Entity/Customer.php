<?php

namespace Alpixel\Bundle\ShopBundle\Tests\Functional\Fixture\Bundle\Entity;

use Alpixel\Bundle\ShopBundle\Entity\Customer as BaseCustomer;
use Doctrine\ORM\Mapping as ORM;

/**
 * User.
 *
 * @ORM\Entity()
 * @ORM\Table(name="alpixel_shop_customer")
 */
class Customer extends BaseCustomer
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    protected $title;

    /**
     * Gets the value of id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the value of id.
     *
     * @param int $id the id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Gets the value of title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the value of title.
     *
     * @param string $title the title
     *
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }
}
