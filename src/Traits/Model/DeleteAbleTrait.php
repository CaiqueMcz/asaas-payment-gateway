<?php

namespace AsaasPaymentGateway\Traits\Model;

use AsaasPaymentGateway\Exception\AsaasException;
use AsaasPaymentGateway\Exception\AsaasValidationException;
use GuzzleHttp\Exception\GuzzleException;

trait DeleteAbleTrait
{
    /**
     * @throws AsaasException
     * @throws GuzzleException
     * @throws AsaasValidationException
     */
    public function delete(): bool
    {
        $this->hasIdOrFails();
        return self::getRepository()->delete($this->getId());
    }
}
