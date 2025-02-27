<?php

namespace CaiqueMcz\AsaasPaymentGateway\Tests\Unit\Repository;

use CaiqueMcz\AsaasPaymentGateway\Http\Client;
use CaiqueMcz\AsaasPaymentGateway\Model\Payment;
use CaiqueMcz\AsaasPaymentGateway\Model\Subscription;
use CaiqueMcz\AsaasPaymentGateway\Repository\SubscriptionRepository;
use CaiqueMcz\AsaasPaymentGateway\Response\ListResponse;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class SubscriptionRepositoryTest extends TestCase
{
    private SubscriptionRepository $repository;
    private MockObject $clientMock;
    private array $validSubscriptionData;
    private string $subscriptionId = 'sub_VXJBYgP2u0eO';

    public function testCreateWithCreditCard(): void
    {
        $creditCardData = [
            'holderName' => 'John Doe',
            'number' => '4111111111111111',
            'expiryMonth' => '12',
            'expiryYear' => '2030',
            'ccv' => '123'
        ];

        $holderInfo = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'cpfCnpj' => '12345678901',
            'postalCode' => '12345678',
            'addressNumber' => '123',
            'addressComplement' => 'Apt 1',
            'phone' => '1234567890',
            'mobilePhone' => '9876543210'
        ];

        $data = array_merge(
            $this->validSubscriptionData,
            [
                'creditCard' => $creditCardData,
                'creditCardHolderInfo' => $holderInfo,
                'remoteIp' => '127.0.0.1'
            ]
        );

        $expectedResponse = array_merge(
            ['id' => $this->subscriptionId],
            $this->validSubscriptionData,
            ['status' => 'ACTIVE']
        );

        $this->clientMock
            ->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo('subscriptions/'),
                $this->callback(function ($data) {
                    return isset($data['customer'], $data['billingType']) && isset($data['creditCard'])
                        && isset($data['creditCardHolderInfo']) && isset($data['remoteIp']);
                })
            )
            ->willReturn($expectedResponse);

        $result = $this->repository->createWithCreditCard($data);

        $this->assertInstanceOf(Subscription::class, $result);
        $this->assertEquals($this->subscriptionId, $result->getId());
        $this->assertEquals('ACTIVE', $result->getStatus());
    }

    public function testCreateWithCreditCardTokenized(): void
    {
        $data = array_merge(
            $this->validSubscriptionData,
            [
                'creditCardToken' => 'uuid',
                'remoteIp' => '127.0.0.1'
            ]
        );

        $expectedResponse = array_merge(
            ['id' => $this->subscriptionId],
            $this->validSubscriptionData,
            ['status' => 'ACTIVE']
        );

        $this->clientMock
            ->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo('subscriptions/'),
                $this->callback(function ($data) {
                    return isset($data['customer'])
                        && isset($data['billingType'])
                        && isset($data['creditCardToken'])
                        && isset($data['remoteIp']);
                })
            )
            ->willReturn($expectedResponse);

        $result = $this->repository->createWithCreditCardTokenized($data);

        $this->assertInstanceOf(Subscription::class, $result);
        $this->assertEquals($this->subscriptionId, $result->getId());
        $this->assertEquals('ACTIVE', $result->getStatus());
    }

    public function testGetPayments(): void
    {
        $expectedResponse = [
            'object' => 'list',
            'hasMore' => false,
            'totalCount' => 2,
            'limit' => 10,
            'offset' => 0,
            'data' => [
                [
                    'id' => 'pay_123456',
                    'customer' => 'cus_0T1mdomVMi39',
                    'value' => 19.9,
                    'billingType' => 'CREDIT_CARD',
                    'status' => 'CONFIRMED',
                    'dueDate' => '2023-12-15'
                ],
                [
                    'id' => 'pay_654321',
                    'customer' => 'cus_0T1mdomVMi39',
                    'value' => 19.9,
                    'billingType' => 'CREDIT_CARD',
                    'status' => 'PENDING',
                    'dueDate' => '2024-01-15'
                ]
            ]
        ];

        $this->clientMock
            ->expects($this->once())
            ->method('get')
            ->with(
                $this->equalTo("subscriptions/{$this->subscriptionId}/payments"),
                $this->equalTo([])
            )
            ->willReturn($expectedResponse);

        $result = $this->repository->getPayments($this->subscriptionId);

        $this->assertInstanceOf(ListResponse::class, $result);
        $this->assertEquals(2, $result->getTotalCount());
        $this->assertEquals(10, $result->getLimit());
        $this->assertCount(2, $result->getRows());

        foreach ($result->getRows() as $payment) {
            $this->assertInstanceOf(Payment::class, $payment);
        }
    }

    public function testGetPaymentBook(): void
    {
        $expectedResponse = ['https://www.asaas.com/paymentBook?id=' . $this->subscriptionId];

        $this->clientMock
            ->expects($this->once())
            ->method('get')
            ->with(
                $this->equalTo("subscriptions/{$this->subscriptionId}/paymentBook"),
                $this->equalTo([])
            )
            ->willReturn($expectedResponse);

        $result = $this->repository->getPaymentBook($this->subscriptionId);

        $this->assertIsString($result);
        $this->assertEquals($expectedResponse[0], $result);
    }

    protected function setUp(): void
    {
        $this->clientMock = $this->createMock(Client::class);
        $this->repository = new SubscriptionRepository(Subscription::class);

        $reflection = new ReflectionClass($this->repository);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($this->repository, $this->clientMock);

        // Setup valid subscription data
        $this->validSubscriptionData = [
            'customer' => 'cus_0T1mdomVMi39',
            'billingType' => 'CREDIT_CARD',
            'value' => 19.9,
            'nextDueDate' => '2023-12-15',
            'cycle' => 'MONTHLY',
            'description' => 'Assinatura Plano Pró',
            'externalReference' => 'REF123'
        ];
    }
}
