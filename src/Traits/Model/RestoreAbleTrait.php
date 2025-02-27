<?php

namespace CaiqueMcz\AsaasPaymentGateway\Traits\Model;

use CaiqueMcz\AsaasPaymentGateway\Exception\AsaasException;
use CaiqueMcz\AsaasPaymentGateway\Model\AbstractModel;
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
