<?php

namespace AsaasPaymentGateway\Traits\Model;

use AsaasPaymentGateway\Exception\AsaasException;
use AsaasPaymentGateway\Helpers\Utils;
use AsaasPaymentGateway\Model\AbstractModel;
use GuzzleHttp\Exception\GuzzleException;

trait UpdateAbleTrait
{
    /**
     * @throws AsaasException
     * @throws GuzzleException
     */
    public function update(array $data): AbstractModel
    {
        $this->hasIdOrFails();

        return self::getRepository()->update($this->getId(), $data);
    }
}
