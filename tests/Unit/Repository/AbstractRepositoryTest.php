<?php

namespace CaiqueMcz\AsaasPaymentGateway\Tests\Unit\Repository;

use CaiqueMcz\AsaasPaymentGateway\Enums\Payments\DiscountType;
use CaiqueMcz\AsaasPaymentGateway\Exception\AsaasException;
use CaiqueMcz\AsaasPaymentGateway\Exception\AsaasValidationException;
use CaiqueMcz\AsaasPaymentGateway\Http\Client;
use CaiqueMcz\AsaasPaymentGateway\Response\ListResponse;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\GatewayTrait;
use CaiqueMcz\AsaasPaymentGateway\Tests\Unit\Repository\AbstractRepository\DummyRepository;
use CaiqueMcz\AsaasPaymentGateway\Tests\Unit\Repository\AbstractRepository\TestModel;
use CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments\Discount;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class AbstractRepositoryTest extends TestCase
{
    use GatewayTrait;

    private DummyRepository $dummyRepository;
    private $mockClient;

    public function testGetDefaultEndpoint()
    {
        $repo = new DummyRepository(TestModel::class, 'testmodels');
        $this->assertEquals('testmodels', $repo->getEndpoint());
    }

    /**
     * @throws GuzzleException
     * @throws AsaasException
     */
    public function testCreate()
    {
        $data = ['id' => 1, 'name' => 'Test'];
        $this->mockClient->method('post')
            ->with('testmodels', $data)
            ->willReturn($data);
        $model = $this->dummyRepository->create($data);
        $this->assertInstanceOf(TestModel::class, $model);
        $this->assertEquals(1, $model->getId());
        $this->assertEquals('Test', $model->getName());
    }

    /**
     * @throws GuzzleException
     * @throws AsaasException
     */
    public function testRestore()
    {
        $data = ['id' => 2, 'name' => 'Restored'];
        $this->mockClient->method('post')
            ->with('testmodels/2/restore', [])
            ->willReturn($data);
        $model = $this->dummyRepository->restore('2');
        $this->assertInstanceOf(TestModel::class, $model);
        $this->assertEquals(2, $model->getId());
        $this->assertEquals('Restored', $model->getName());
    }

    /**
     * @throws AsaasException
     * @throws GuzzleException
     * @throws AsaasValidationException
     */
    public function testDelete()
    {
        $response = ['deleted' => true];
        $this->mockClient->method('delete')
            ->with('testmodels/3')
            ->willReturn($response);
        $result = $this->dummyRepository->delete('3');
        $this->assertTrue($result);
    }

    /**
     * @throws AsaasException
     * @throws GuzzleException
     */
    public function testUpdate()
    {
        $data = ['name' => 'Updated'];
        $response = ['id' => 4, 'name' => 'Updated'];
        $this->mockClient->method('put')
            ->with('testmodels/4', $data)
            ->willReturn($response);
        $model = $this->dummyRepository->update('4', $data);
        $this->assertInstanceOf(TestModel::class, $model);
        $this->assertEquals(4, $model->getId());
        $this->assertEquals('Updated', $model->getName());
    }

    /**
     * @throws AsaasException
     */
    public function testGetById()
    {
        $data = ['id' => 5, 'name' => 'Found'];
        $this->mockClient->method('get')
            ->with('testmodels/5')
            ->willReturn($data);
        $model = $this->dummyRepository->getById('5');
        $this->assertInstanceOf(TestModel::class, $model);
        $this->assertEquals(5, $model->getId());
        $this->assertEquals('Found', $model->getName());
    }

    /**
     * @throws AsaasException
     */
    public function testGet()
    {
        $data = ['id' => 6, 'name' => 'Default'];
        $this->mockClient->method('get')
            ->with('testmodels')
            ->willReturn($data);
        $model = $this->dummyRepository->get();
        $this->assertInstanceOf(TestModel::class, $model);
        $this->assertEquals(6, $model->getId());
        $this->assertEquals('Default', $model->getName());
    }

    /**
     * @throws AsaasException
     */
    public function testList()
    {
        $listResponseData = [
            'object' => 'list',
            'hasMore' => false,
            'totalCount' => 1,
            'limit' => 1,
            'offset' => 0,
            'data' => [['id' => 7, 'name' => 'ListItem']]
        ];
        $filters = ['param' => 'value'];
        $this->mockClient->method('get')
            ->with('testmodels', $filters)
            ->willReturn($listResponseData);
        $listResponse = $this->dummyRepository->list($filters);
        $this->assertInstanceOf(ListResponse::class, $listResponse);
        $array = $listResponse->toArray();
        $this->assertEquals('list', $array['object']);
        $this->assertFalse($array['hasMore']);
        $this->assertEquals(1, $array['totalCount']);
        $this->assertEquals(1, $array['limit']);
        $this->assertEquals(0, $array['offset']);
        $this->assertNotEmpty($array['data']);
    }

    public function testPreparerData()
    {
        $repo = new DummyRepository(TestModel::class, 'testmodels');

        $data = ['name' => 'Test', 'discount' => new Discount(
            10,
            1,
            DiscountType::PERCENTAGE()
        )];
        $parsedData = $repo->prepareSendData($data);

        $this->assertEquals("Test", $parsedData['name']);
        $this->assertEquals(10, $parsedData['discount']['value']);
        $this->assertEquals(1, $parsedData['discount']['dueDateLimitDays']);
        $this->assertEquals(DiscountType::PERCENTAGE(), $parsedData['discount']['type']);
    }

    protected function setUp(): void
    {
        $this->initGateway();
        $this->mockClient = $this->createMock(Client::class);
        $this->dummyRepository = new DummyRepository(TestModel::class, 'testmodels');
        $ref = new ReflectionClass($this->dummyRepository);
        $prop = $ref->getProperty('client');
        $prop->setAccessible(true);
        $prop->setValue($this->dummyRepository, $this->mockClient);
    }
}
