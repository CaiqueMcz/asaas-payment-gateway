<?php

namespace AsaasPaymentGateway\Tests\Integration\Model;

use AsaasPaymentGateway\Model\AbstractModel;
use PHPUnit\Framework\TestCase;

abstract class AbstractTestModel extends TestCase implements ModelInterface
{
    public function processShow(AbstractModel $entity)
    {
    }
}
