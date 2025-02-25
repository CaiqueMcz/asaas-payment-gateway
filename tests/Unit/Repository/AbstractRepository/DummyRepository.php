<?php

namespace AsaasPaymentGateway\Tests\Unit\Repository\AbstractRepository;

use AsaasPaymentGateway\Repository\AbstractRepository;

class DummyRepository extends AbstractRepository
{
    public function __construct(string $modelClass, ?string $endpoint = null)
    {
        parent::__construct($modelClass, $endpoint);
    }
}
