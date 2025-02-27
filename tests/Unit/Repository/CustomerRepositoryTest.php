<?php

namespace CaiqueMcz\AsaasPaymentGateway\Tests\Unit\Repository;

use CaiqueMcz\AsaasPaymentGateway\Exception\AsaasException;
use CaiqueMcz\AsaasPaymentGateway\Http\Client;
use CaiqueMcz\AsaasPaymentGateway\Model\Customer;
use CaiqueMcz\AsaasPaymentGateway\Repository\CustomerRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CustomerRepositoryTest extends TestCase
{
    private CustomerRepository $repository;
    private MockObject $clientMock;
    private array $validCustomerData;
    private string $customerId = 'cus_123456789';

    public function testCreateCustomerWithValidData(): void
    {
        $expectedResponse = array_merge(
            ['id' => $this->customerId],
            $this->validCustomerData
        );

        $this->clientMock
            ->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo('customers'),
                $this->callback(function ($data) {
                    return isset($data['name'])
                        && isset($data['cpfCnpj'])
                        && isset($data['email']);
                })
            )
            ->willReturn($expectedResponse);

        $result = $this->repository->create($this->validCustomerData);

        $this->assertInstanceOf(Customer::class, $result);
        $this->assertEquals($this->customerId, $result->getId());
        $this->assertEquals($this->validCustomerData['name'], $result->getName());
        $this->assertEquals($this->validCustomerData['cpfCnpj'], $result->getCpfCnpj());
        $this->assertEquals($this->validCustomerData['email'], $result->getEmail());
    }


    public function testUpdateCustomerWithValidData(): void
    {
        $updateData = [
            'name' => 'John Doe Updated',
            'email' => 'john.updated@example.com'
        ];

        $expectedResponse = array_merge(
            ['id' => $this->customerId],
            $this->validCustomerData,
            $updateData
        );

        $this->clientMock
            ->expects($this->once())
            ->method('put')
            ->with(
                $this->equalTo("customers/{$this->customerId}"),
                $this->equalTo($updateData)
            )
            ->willReturn($expectedResponse);

        $result = $this->repository->update($this->customerId, $updateData);

        $this->assertInstanceOf(Customer::class, $result);
        $this->assertEquals($updateData['name'], $result->getName());
        $this->assertEquals($updateData['email'], $result->getEmail());
    }

    public function testGetCustomerById(): void
    {
        $expectedResponse = array_merge(
            ['id' => $this->customerId],
            $this->validCustomerData
        );

        $this->clientMock
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo("customers/{$this->customerId}"))
            ->willReturn($expectedResponse);

        $result = $this->repository->getById($this->customerId);

        $this->assertInstanceOf(Customer::class, $result);
        $this->assertEquals($this->customerId, $result->getId());
        $this->assertEquals($this->validCustomerData['name'], $result->getName());
    }

    public function testListCustomers(): void
    {
        $expectedResponse = [
            'object' => 'list',
            'hasMore' => false,
            'totalCount' => 1,
            'limit' => 10,
            'offset' => 0,
            'data' => [
                array_merge(['id' => $this->customerId], $this->validCustomerData)
            ]
        ];

        $this->clientMock
            ->expects($this->once())
            ->method('get')
            ->with(
                $this->equalTo('customers'),
                $this->equalTo([])
            )
            ->willReturn($expectedResponse);

        $result = $this->repository->list();

        $this->assertEquals('list', $result->getObject());
        $this->assertFalse($result->hasMore());
        $this->assertEquals(1, $result->getTotalCount());

        $customers = $result->getRows();
        $this->assertCount(1, $customers);
        $this->assertInstanceOf(Customer::class, $customers[0]);
    }

    public function testListCustomersWithFilters(): void
    {
        $filters = [
            'name' => 'John',
            'email' => 'john@example.com'
        ];

        $expectedResponse = [
            'object' => 'list',
            'hasMore' => false,
            'totalCount' => 1,
            'limit' => 10,
            'offset' => 0,
            'data' => [
                array_merge(['id' => $this->customerId], $this->validCustomerData)
            ]
        ];

        $this->clientMock
            ->expects($this->once())
            ->method('get')
            ->with(
                $this->equalTo('customers'),
                $this->equalTo($filters)
            )
            ->willReturn($expectedResponse);

        $result = $this->repository->list($filters);

        $this->assertEquals(1, $result->getTotalCount());
        $customers = $result->getRows();
        $this->assertCount(1, $customers);
    }

    public function testDeleteCustomer(): void
    {
        $expectedResponse = [
            'deleted' => true,
            'id' => $this->customerId
        ];

        $this->clientMock
            ->expects($this->once())
            ->method('delete')
            ->with($this->equalTo("customers/{$this->customerId}"))
            ->willReturn($expectedResponse);

        $result = $this->repository->delete($this->customerId);

        $this->assertTrue($result);
    }

    public function testRestoreCustomer(): void
    {
        $expectedResponse = array_merge(
            ['id' => $this->customerId, 'deleted' => false],
            $this->validCustomerData
        );

        $this->clientMock
            ->expects($this->once())
            ->method('post')
            ->with($this->equalTo("customers/{$this->customerId}/restore"))
            ->willReturn($expectedResponse);

        $result = $this->repository->restore($this->customerId);

        $this->assertInstanceOf(Customer::class, $result);
        $this->assertEquals($this->customerId, $result->getId());
        $this->assertFalse($result->isDeleted());
    }


    public function testPrepareSendDataFormatsDataCorrectly(): void
    {
        $reflection = new \ReflectionClass($this->repository);
        $method = $reflection->getMethod('prepareSendData');
        $method->setAccessible(true);

        $result = $method->invoke($this->repository, $this->validCustomerData);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('cpfCnpj', $result);
        $this->assertArrayHasKey('email', $result);
        $this->assertIsBool($result['notificationDisabled']);
    }

    protected function setUp(): void
    {
        $this->clientMock = $this->createMock(Client::class);
        $this->repository = new CustomerRepository(Customer::class);

        $reflection = new \ReflectionClass($this->repository);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($this->repository, $this->clientMock);

        // Setup valid customer data
        $this->validCustomerData = [
            'name' => 'John Doe',
            'cpfCnpj' => '12345678901',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'mobilePhone' => '1234567890',
            'address' => 'Street Name',
            'addressNumber' => '123',
            'complement' => 'Apt 4B',
            'province' => 'Downtown',
            'postalCode' => '12345678',
            'externalReference' => 'REF123',
            'notificationDisabled' => false,
            'additionalEmails' => 'secondary@example.com',
            'municipalInscription' => '987654',
            'stateInscription' => '654321',
            'observations' => 'Test customer'
        ];
    }
}
