<?php

namespace AsaasPaymentGateway\Tests\Traits\Model;

use AsaasPaymentGateway\Model\AbstractModel;

trait RestoreAbleTrait
{
    public function processRestore(AbstractModel $entity): AbstractModel
    {
        // Assert that the restored entity has a non-null ID.
        $this->assertNotNull($entity->getId(), 'ID should not be null after creation');

        // Get the model class to be used for creation.
        $entityClass = $this->getModelClass();
        // Generate random data for the entity.
        $data = $this->getRandomData();
        $this->assertTrue($entity->isDeleted());
        if ($this->withMock === true) {
            $data['deleted'] = false;
            $restoredEntity = call_user_func([$entityClass, 'fromArray'], $data);
            $this->mockRepository
                ->expects($this->once())
                ->method('restore')
                ->with($this->equalTo($entity->getId()))
                ->willReturn($restoredEntity);
        }
        $restoredEntity = $entity->restore();

        $this->assertFalse($restoredEntity->isDeleted());

        return $restoredEntity;
    }
}
