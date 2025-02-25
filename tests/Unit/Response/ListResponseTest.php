<?php

namespace AsaasPaymentGateway\Tests\Unit\Response;

use AsaasPaymentGateway\Model\AbstractModel;
use AsaasPaymentGateway\Model\Customer;
use AsaasPaymentGateway\Response\ListResponse;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class ListResponseTest extends TestCase
{
    private array $sampleData;

    protected function setUp(): void
    {
        $this->sampleData = [
            'modelClass' => Customer::class,
            'object' => 'list',
            'hasMore' => true,
            'totalCount' => 100,
            'limit' => 10,
            'offset' => 0,
            'data' => [
                [
                    'id' => '1',
                    'name' => 'Customer 1',
                    'email' => 'customer1@example.com'
                ],
                [
                    'id' => '2',
                    'name' => 'Customer 2',
                    'email' => 'customer2@example.com'
                ]
            ]
        ];
    }

    public function testFromArrayWithCompleteData(): void
    {
        $response = ListResponse::fromArray($this->sampleData);

        $this->assertInstanceOf(ListResponse::class, $response);
        $this->assertEquals('list', $response->getObject());
        $this->assertTrue($response->hasMore());
        $this->assertEquals(100, $response->getTotalCount());
        $this->assertEquals(10, $response->getLimit());
        $this->assertEquals(0, $response->getOffset());
    }

    public function testFromArrayWithMinimalData(): void
    {
        $minimalData = [
            'modelClass' => Customer::class
        ];

        $response = ListResponse::fromArray($minimalData);

        $this->assertInstanceOf(ListResponse::class, $response);
        $this->assertEquals('list', $response->getObject());
        $this->assertFalse($response->hasMore());
        $this->assertEquals(0, $response->getTotalCount());
        $this->assertEquals(0, $response->getLimit());
        $this->assertEquals(0, $response->getOffset());
        $this->assertEmpty($response->toArray()['data']);
    }

    public function testToArray(): void
    {
        $response = ListResponse::fromArray($this->sampleData);
        $array = $response->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('object', $array);
        $this->assertArrayHasKey('hasMore', $array);
        $this->assertArrayHasKey('totalCount', $array);
        $this->assertArrayHasKey('limit', $array);
        $this->assertArrayHasKey('offset', $array);
        $this->assertArrayHasKey('data', $array);

        $this->assertEquals('list', $array['object']);
        $this->assertTrue($array['hasMore']);
        $this->assertEquals(100, $array['totalCount']);
        $this->assertEquals(10, $array['limit']);
        $this->assertEquals(0, $array['offset']);
        $this->assertCount(2, $array['data']);
    }

    public function testGetRows(): void
    {
        // Criar um mock estático do AbstractModel usando reflection
        $reflectionClass = new ReflectionClass(AbstractModel::class);
        $parseRowsMethod = $reflectionClass->getMethod('parseRows');
        $parseRowsMethod->setAccessible(true);


        $response = ListResponse::fromArray($this->sampleData);
        $rows = $response->getRows();

        $this->assertIsArray($rows);
        $this->assertCount(2, $rows);

        // Verificar se os dados foram convertidos corretamente
        foreach ($rows as $row) {
            $this->assertInstanceOf(Customer::class, $row);
            $this->assertContains($row->getId(), ['1', '2']);
            $this->assertContains($row->getName(), ['Customer 1', 'Customer 2']);
            $this->assertContains($row->getEmail(), ['customer1@example.com', 'customer2@example.com']);
        }
    }

    public function testGettersReturnCorrectTypes(): void
    {
        $response = ListResponse::fromArray($this->sampleData);

        $this->assertIsString($response->getObject());
        $this->assertIsBool($response->hasMore());
        $this->assertIsInt($response->getTotalCount());
        $this->assertIsInt($response->getLimit());
        $this->assertIsInt($response->getOffset());
    }

    public function testFromArrayWithCustomObject(): void
    {
        $customData = $this->sampleData;
        $customData['object'] = 'custom_list';

        $response = ListResponse::fromArray($customData);

        $this->assertEquals('custom_list', $response->getObject());
    }

    public function testFromArrayWithEmptyData(): void
    {
        $emptyData = [
            'modelClass' => Customer::class,
            'data' => []
        ];

        $response = ListResponse::fromArray($emptyData);

        $this->assertInstanceOf(ListResponse::class, $response);
        $this->assertEquals('list', $response->getObject());
        $this->assertFalse($response->hasMore());
        $this->assertEquals(0, $response->getTotalCount());
        $this->assertEmpty($response->toArray()['data']);
    }
}
