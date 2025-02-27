<?php

namespace CaiqueMcz\AsaasPaymentGateway\Tests\Traits\Model;

use CaiqueMcz\AsaasPaymentGateway\Exception\AsaasPageNotFoundException;
use CaiqueMcz\AsaasPaymentGateway\Model\AbstractModel;

trait ShowAbleTrait
{
    public function processShow(AbstractModel $entity): AbstractModel
    {
        $entityClass = $this->getModelClass();
        if ($this->withMock === true) {
            $endpoint = $this->mockRepository->endpoint;
            $this->addInterceptor("get", $endpoint . '/' . $entity->getId(), $entity->toArray());
        }
        $response = call_user_func([$entityClass, 'getById'], $entity->getId());
        $this->assertInstanceOf($entityClass, $response);
        $this->assertEquals($entity->getId(), $response->getId());
        return $response;
    }

    public function processShowWithInvalidId(): void
    {
        $entityClass = $this->getModelClass();
        call_user_func([$entityClass, 'resetRepository'], $entityClass);
        $invalidId = "invalid-id";
        $this->expectException(AsaasPageNotFoundException::class);
        call_user_func([$entityClass, 'getById'], $invalidId);
    }
}
