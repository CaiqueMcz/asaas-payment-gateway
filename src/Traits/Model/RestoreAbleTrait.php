<?php

namespace AsaasPaymentGateway\Traits\Model;

use AsaasPaymentGateway\Exception\AsaasException;
use AsaasPaymentGateway\Model\AbstractModel;
use GuzzleHttp\Exception\GuzzleException;

trait RestoreAbleTrait
{
    /**
     * @throws AsaasException
     * @throws GuzzleException
     */
    public function restore(): AbstractModel
    {
        $this->hasIdOrFails();
        return self::getRepository()->restore($this->getId());
    }
}
