<?php

namespace CaiqueMcz\AsaasPaymentGateway\Tests\Traits\Model;

use CaiqueMcz\AsaasPaymentGateway\Model\AbstractModel;

trait CreateAbleTrait
{
    public function processCreate(?array $randomData = null, $customMethodName = null): AbstractModel
    {
        // Get the model class to be used for creation.
        $entityClass = $this->getModelClass();
        // Generate random data for the entity.
        if (is_null($randomData)) {
            $data = $this->getRandomData();
        } else {
            $data = $randomData;
        }
        if (is_null($customMethodName)) {
            $customMethodName = "create";
        }

        if ($this->withMock === true) {
            $data['id'] = 1;
            $expectedCreatedEntity = call_user_func([$entityClass, 'fromArray'], $data);
            $this->mockRepository
                ->expects($this->once())
                ->method($customMethodName)
                ->willReturn($expectedCreatedEntity);
        }
        $createdEntity = call_user_func([$entityClass, $customMethodName], $data);
        // Assert that the created entity has a non-null ID.
        $this->assertNotNull($createdEntity->getId(), 'ID should not be null after creation');
        // Retrieve field information for validation.
        $methods = $this->getFieldInfos();
        // Loop through each field to check if the values match the data.
        foreach ($methods as $key => $method) {
            if (!isset($data[$key]) || $key === 'nextDueDate') {
                continue;
            }
            // Get the getter function name for the field.
            $functionName = $method['get'];
            // Assert that the value in the created entity matches the random data.
            $val = $createdEntity->$functionName();

            $this->assertEquals($data[$key], $val);
        }
        // Return the created entity.
        return $createdEntity;
    }
}
