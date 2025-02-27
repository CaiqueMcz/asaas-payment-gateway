<?php

namespace CaiqueMcz\AsaasPaymentGateway\Traits\Model;

use CaiqueMcz\AsaasPaymentGateway\Exception\AsaasException;
use CaiqueMcz\AsaasPaymentGateway\Exception\AsaasValidationException;
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
