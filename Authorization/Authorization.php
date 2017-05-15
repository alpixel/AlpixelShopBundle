<?php

namespace Alpixel\Bundle\ShopBundle\Authorization;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

/**
 * @author Alexis BUSSIERES <alexis@alpixel.fr>
 */
class Authorization
{
    /**
     * @var bool
     */
    protected $isAuthorized = false;

    public function __construct(TokenStorage $tokenStorage, AuthorizationChecker $checker, $authorizedRoles = [])
    {
        if ($tokenStorage->getToken() !== null) {
            $user = $tokenStorage->getToken()->getUser();
            if ($user !== null) {
                $this->isAuthorized = $checker->isGranted($authorizedRoles);
            }
        }
    }

    public function isAuthorized()
    {
        return $this->isAuthorized;
    }
}
