<?php

namespace CaiqueMcz\AsaasPaymentGateway\Exception;

use Exception;

class AsaasPageNotFoundException extends Exception
{
    public function __construct($message = "Page not found", $code = 404)
    {
        parent::__construct($message, $code);
    }
}
