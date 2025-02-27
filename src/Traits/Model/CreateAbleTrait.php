<?php

namespace CaiqueMcz\AsaasPaymentGateway\Traits\Model;

use CaiqueMcz\AsaasPaymentGateway\Helpers\Utils;
use CaiqueMcz\AsaasPaymentGateway\Model\AbstractModel;

trait CreateAbleTrait
{
    public static function create(array $data): ?AbstractModel
    {
        return static::getRepository()->create($data);
    }
}
