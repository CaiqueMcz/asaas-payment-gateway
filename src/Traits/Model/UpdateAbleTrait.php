<?php

namespace CaiqueMcz\AsaasPaymentGateway\Traits\Model;

use CaiqueMcz\AsaasPaymentGateway\Exception\AsaasException;
use CaiqueMcz\AsaasPaymentGateway\Helpers\Utils;
use CaiqueMcz\AsaasPaymentGateway\Model\AbstractModel;
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
