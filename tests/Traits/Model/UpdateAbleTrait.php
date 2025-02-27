<?php

namespace CaiqueMcz\AsaasPaymentGateway\Tests\Traits\Model;

use CaiqueMcz\AsaasPaymentGateway\Model\AbstractModel;

trait UpdateAbleTrait
{
    public function processUpdate(AbstractModel $entity)
    {
        // Get the default column to update.
        $col = $this->defaultCol;
        // Retrieve field information for the specified column.
        $fieldInfos = $this->getFieldInfos()[$col];
        // Get the setter function name for the field.
        $setFunctionName = $fieldInfos['set'];
        // Get the getter function name for the field.
        $getFunctionName = $fieldInfos['get'];
        // Generate a new random value for the field.
        $newVal = $this->getRandomData()[$col];

        // Assert that the new value is different from the current value.
        $this->assertNotEquals($newVal, $entity->$getFunctionName());
        // Set the new value on the entity.
        $entity->$setFunctionName($newVal);
        if ($this->withMock === true) {
            $expectedData = $entity->toArray();
            $expectedData[$col] = $newVal;
            $refreshedCustomer = call_user_func([$this->getModelClass(), 'fromArray'], $expectedData);
            $this->mockRepository
                ->expects($this->once())
                ->method('update')
                ->with($this->equalTo($entity->getId()))
                ->willReturn($refreshedCustomer);
        }

        $refreshedCustomer = $entity->update($entity->toArray());
        // Assert that the updated value matches the new value.
        $this->assertEquals($newVal, $refreshedCustomer->$getFunctionName());
        // Return the refreshed entity.
        return $refreshedCustomer;
    }
}
