<?php

namespace CaiqueMcz\AsaasPaymentGateway\Tests\Unit\Model\AbstractModel;

use CaiqueMcz\AsaasPaymentGateway\Model\AbstractModel;

class TestModel extends AbstractModel
{
    protected array $fields = ['id', 'name', 'age', 'active'];
    protected array $requiredFields = ['name'];
    protected array $casts = [
        'id' => 'int',
        'age' => 'int',
        'active' => 'bool',
    ];
}
