<?php

namespace CaiqueMcz\AsaasPaymentGateway\Tests\Traits;

use CaiqueMcz\AsaasPaymentGateway\Model\AbstractModel;

trait CreditCardTrait
{
    public function tokenizeCreditCard(): AbstractModel
    {
        // Get the model class to be used for creation.
        $entityClass = $this->getModelClass();
        $data = $this->getRandomData();

        if ($this->withMock === true) {
            $expectedCreatedEntity = [
                'creditCardNumber' => substr($data['creditCard']->getNumber(), -4),
                'creditCardBrand' => 'visa',
                'creditCardToken' => $this->faker->uuid
            ];
            $expectedCreatedEntity = array_merge($expectedCreatedEntity, $data);
            $expectedCreatedEntity = call_user_func([$entityClass, 'fromArray'], $expectedCreatedEntity);
            $this->mockRepository
                ->expects($this->once())
                ->method('tokenizeCreditCard')
                ->willReturn($expectedCreatedEntity);
        }
        $entity = call_user_func([$entityClass, 'fromArray'], $data);
        $createdEntity = $entity->tokenizeCreditCard();
        $this->assertNotNull($createdEntity->getCreditCardToken());
        $this->assertNotNull($createdEntity->getCreditCardBrand());

        return $createdEntity;
    }
}
