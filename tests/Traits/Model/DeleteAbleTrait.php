<?php

namespace CaiqueMcz\AsaasPaymentGateway\Tests\Traits\Model;

use CaiqueMcz\AsaasPaymentGateway\Exception\AsaasException;
use CaiqueMcz\AsaasPaymentGateway\Exception\AsaasValidationException;
use CaiqueMcz\AsaasPaymentGateway\Model\AbstractModel;
use CaiqueMcz\AsaasPaymentGateway\Model\Payment;
use GuzzleHttp\Exception\GuzzleException;

trait DeleteAbleTrait
{

    /**
     * @throws AsaasException
     * @throws GuzzleException
     * @throws AsaasValidationException
     */
    public function processDelete(AbstractModel $entity): AbstractModel
    {
        $entityClass = $this->getModelClass();
        $entityAsArray = $entity->toArray();
        if ($this->withMock === true) {
            $this->mockRepository
                ->expects($this->once())
                ->method('delete')
                ->with($this->equalTo($entity->getId()))
                ->willReturn(true);
        }
        if ($this->withMock === false) {
            $entity = $entity->refresh();
        }
        $this->assertNotNull($entity->getId(), 'ID should not be null after creation');
        $this->assertTrue($entity->delete());
        if ($this->withMock === false) {
            $entity = $entity->refresh();
        } else {
            $entityAsArray['deleted'] = true;
            $entity = call_user_func([$entityClass, 'fromArray'], $entityAsArray);
        }
        return $entity;
    }
}
