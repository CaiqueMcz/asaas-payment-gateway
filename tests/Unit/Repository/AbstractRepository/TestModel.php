<?php

namespace CaiqueMcz\AsaasPaymentGateway\Tests\Unit\Repository\AbstractRepository;

use CaiqueMcz\AsaasPaymentGateway\Model\AbstractModel;
use CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments\Discount;

class TestModel extends AbstractModel
{
    protected array $fields = ['id', 'name'];
    protected array $requiredFields = ['name'];
    protected array $casts = [
        'id' => 'int',
        'name' => 'string',
        'discount' => Discount::class
    ];
}
