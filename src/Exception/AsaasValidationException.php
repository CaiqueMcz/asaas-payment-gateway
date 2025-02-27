<?php

namespace CaiqueMcz\AsaasPaymentGateway\Exception;

use CaiqueMcz\AsaasPaymentGateway\Helpers\Utils;
use Exception;

class AsaasValidationException extends Exception
{
    private array $errors;

    public function __construct(array $errors)
    {
        $this->errors = $errors;
        $error = $this->getFirstError();
        parent::__construct($error['description']);
    }

    public function getFirstError()
    {
        return $this->errors[0];
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
