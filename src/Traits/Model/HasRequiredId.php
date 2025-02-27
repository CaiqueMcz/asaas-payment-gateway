<?php

namespace CaiqueMcz\AsaasPaymentGateway\Traits\Model;

use CaiqueMcz\AsaasPaymentGateway\Exception\AsaasException;

trait HasRequiredId
{
    public function hasIdOrFails(): void
    {
        if ($this->getId() === null) {
            AsaasException::requiredFieldException("id");
        }
    }
}
