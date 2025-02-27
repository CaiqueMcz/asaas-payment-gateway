<?php

namespace CaiqueMcz\AsaasPaymentGateway\Tests\Traits\Model;

use CaiqueMcz\AsaasPaymentGateway\Model\AbstractModel;
use CaiqueMcz\AsaasPaymentGateway\Response\ListResponse;

trait ListAbleTrait
{
    public function processList(): AbstractModel
    {
        $entityClass = $this->getModelClass();
        if ($this->withMock === true) {
            $data = $this->getRandomData();
            $entity = call_user_func([$entityClass, 'fromArray'], $data);
            $listData = [];
            $listData['modelClass'] = $this->getModelClass();
            $listData['totalCount'] = 1;
            $row = $entity->toArray();
            $row['id'] = $this->faker->uuid;
            $listData['data'] = [
                $row
            ];
            $endpoint = $this->mockRepository->endpoint;
            $this->addInterceptor("get", $endpoint, $listData);
        }
        /***
         * @var \AsaasPaymentGateway\Response\ListResponse $response
         ***/
        $response = call_user_func([$entityClass, 'get']);
        $this->assertInstanceOf(ListResponse::class, $response);
        $this->assertGreaterThan(0, $response->getTotalCount());
        $firstRow = $response->getFirstRow();
        $this->assertInstanceOf($entityClass, $firstRow);
        return $firstRow;
    }
}
