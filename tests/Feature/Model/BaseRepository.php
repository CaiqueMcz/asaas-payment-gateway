<?php

namespace CaiqueMcz\AsaasPaymentGateway\Tests\Feature\Model;

use CaiqueMcz\AsaasPaymentGateway\Model\AbstractModel;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\GatewayTrait;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\Model\CreateAbleTrait;
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
