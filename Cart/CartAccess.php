<?php

namespace Alpixel\Bundle\ShopBundle\Cart;

use Alpixel\Bundle\ShopBundle\Model\CustomerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class CartAccess
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var null|\Symfony\Component\Security\Core\Authentication\Token\TokenInterface
     */
    private $token;

    /**
     * @var array An array of configuration security
     */
    private $configuration = [];

    /**
     * @var mixed
     */
    private $user;

    /**
     * CartAccess constructor.
     * @param $configuration
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param TokenStorageInterface $token
     */
    public function __construct($configuration, AuthorizationCheckerInterface $authorizationChecker, TokenStorageInterface $token)
    {
        $this->configuration = $configuration;
        $this->authorizationChecker = $authorizationChecker;
        $this->token = $token->getToken();

        if ($this->token !== null) {
            $this->user = $this->token->getUser();
        }
    }

    /**
     * Check if the current is authorize to manage a cart
     *
     * @return bool
     */
    public function isAuthorized()
    {
        return !$this->user === null || $this->isGranted() && $this->getUser() instanceof CustomerInterface;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Check if user has roles define in "alpixe_shop" configuration
     * @return bool
     */
    public function isGranted()
    {
        $roles = $this->configuration['cart_access']['roles'];

        return $this->authorizationChecker->isGranted($roles);
    }
}
