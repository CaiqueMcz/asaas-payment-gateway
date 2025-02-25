<?php

namespace AsaasPaymentGateway\Exception;

use Exception;

class AsaasException extends Exception
{
    public static function undefinedPropertyException($field): self
    {
        return new self("Undefined property: $field");
    }

    public static function requiredFieldException($field): self
    {
        return new self("Field '$field' is required.");
    }
}
