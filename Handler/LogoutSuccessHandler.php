<?php

namespace Alpixel\Bundle\ShopBundle\Handler;

use Alpixel\Bundle\ShopBundle\Cart\SessionCart;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

class LogoutSuccessHandler implements LogoutSuccessHandlerInterface
{
    private $sessionCart;
    private $router;

    public function __construct(SessionCart $sessionCart, Router $router)
    {
        $this->sessionCart = $sessionCart;
        $this->router = $router;
    }

    public function onLogoutSuccess(Request $request)
    {
        $this->sessionCart->remove();

        return new RedirectResponse($this->router->generate('shop'));
    }
}
