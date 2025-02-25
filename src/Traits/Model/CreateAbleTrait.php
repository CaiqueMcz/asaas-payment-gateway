<?php

namespace AsaasPaymentGateway\Traits\Model;

use AsaasPaymentGateway\Helpers\Utils;
use AsaasPaymentGateway\Model\AbstractModel;

trait CreateAbleTrait
{
    public static function create(array $data): ?AbstractModel
    {
        return static::getRepository()->create($data);
    }
}
