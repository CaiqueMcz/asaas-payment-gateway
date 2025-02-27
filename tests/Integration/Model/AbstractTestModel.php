<?php

namespace CaiqueMcz\AsaasPaymentGateway\Tests\Integration\Model;

use CaiqueMcz\AsaasPaymentGateway\Model\AbstractModel;
use PHPUnit\Framework\TestCase;

abstract class AbstractTestModel extends TestCase implements ModelInterface
{
    public function processShow(AbstractModel $entity)
    {
    }
}
