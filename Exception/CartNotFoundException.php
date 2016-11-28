<?php

namespace Alpixel\Bundle\ShopBundle\Exception;


/**
 * @author Alexis BUSSIERES <alexis@alpixel.fr>
 */
class CartNotFoundException extends \Exception
{
    public function __construct($message = "", $code = 0, \Exception $previous = null)
    {
        if ($message === "") {
            $message = 'Cart not found.';
        }

        parent::__construct($message, $code, $previous);
    }
}
