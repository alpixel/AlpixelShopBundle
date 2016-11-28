<?php

namespace Alpixel\Bundle\ShopBundle\Exception;


/**
 * @author Alexis BUSSIERES <alexis@alpixel.fr>
 */
class CartAccessDeniedException extends \Exception
{
    public function __construct($message = "", $code = 0, \Exception $previous = null)
    {
        if ($message === "") {
            $message = 'Access cart denied.';
        }

        parent::__construct($message, $code, $previous);
    }
}
