<?php

namespace CaiqueMcz\AsaasPaymentGateway\Tests\Traits\Model;

use CaiqueMcz\AsaasPaymentGateway\Model\AbstractModel;
use CaiqueMcz\AsaasPaymentGateway\Response\ListResponse;

trait SearchAbleTrait
{
    public function processSearch(AbstractModel $entity): AbstractModel
    {
        // Get the default column used for searching.
        $searchCol = $this->defaultCol;
        // Retrieve the model class to be searched.
        $entityClass = $this->getModelClass();
        // Get field information for the search column.
        $fieldInfos = $this->getFieldInfos()[$searchCol];
        // Get the getter function name for the search column.
        $functionName = $fieldInfos['get'];
        if ($this->withMock === true) {
            $listData = [];
            $listData['modelClass'] = $this->getModelClass();
            $listData['totalCount'] = 1;
            $listData['data'] = [$entity->toArray()];
            $listResponse = ListResponse::fromArray($listData);
            $this->mockRepository
                ->expects($this->once())
                ->method('list')
                ->willReturn($listResponse);
        }
        $foundEntity = call_user_func([$entityClass, 'where'], $searchCol, $entity->$functionName())->first();
        // Assert that a matching entity was found.
        $this->assertNotNull($foundEntity);
        // Assert that the found entity's ID matches the provided entity's ID.
        $this->assertEquals($foundEntity->getId(), $entity->getId());
        // Return the found entity.
        return $foundEntity;
    }
}
