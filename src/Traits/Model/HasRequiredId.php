<?php

namespace AsaasPaymentGateway\Traits\Model;

use AsaasPaymentGateway\Exception\AsaasException;

trait HasRequiredId
{
    public function hasIdOrFails(): void
    {
        if ($this->getId() === null) {
            AsaasException::requiredFieldException("id");
        }
    }
}
