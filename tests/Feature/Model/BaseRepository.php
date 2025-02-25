<?php

namespace AsaasPaymentGateway\Tests\Feature\Model;

use AsaasPaymentGateway\Model\AbstractModel;
use AsaasPaymentGateway\Tests\Traits\GatewayTrait;
use AsaasPaymentGateway\Tests\Traits\Model\CreateAbleTrait;
use PHPUnit\Framework\TestCase;

abstract class BaseRepository extends TestCase implements RepositoryTestInterface
{
    use GatewayTrait;
    use CreateAbleTrait;

    private $mockRepository;

    public function processActionCreate(): ?AbstractModel
    {
        $entityClass = $this->getModelClass();
        call_user_func([$entityClass, 'injectRepository'], $this->getModelClass(), $this->mockRepository);
        return $this->processCreate();
    }
}
