<?php

namespace CaiqueMcz\AsaasPaymentGateway\Tests\Unit\Repository;

use CaiqueMcz\AsaasPaymentGateway\Exception\AsaasException;
use CaiqueMcz\AsaasPaymentGateway\Http\Client;
use CaiqueMcz\AsaasPaymentGateway\Model\CreditCard;
use CaiqueMcz\AsaasPaymentGateway\Repository\CreditCardRepository;
use CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments\CreditCard as CreditCardVO;
use CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments\CreditCardHolderInfo;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class CreditCardRepositoryTest extends TestCase
{
    private CreditCardRepository $repository;
    private MockObject $clientMock;
    private array $validCreditCardData;
    private array $validHolderInfo;

    protected function setUp(): void
    {
        $this->clientMock = $this->createMock(Client::class);
        $this->repository = new CreditCardRepository(CreditCard::class);

        $reflection = new \ReflectionClass($this->repository);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($this->repository, $this->clientMock);

        // Setup valid credit card data
        $this->validCreditCardData = [
            'customer' => 'cus_123',
            'creditCard' => new CreditCardVO(
                '4111111111111111',
                'John Doe',
                '12',
                '2025',
                '123'
            ),
            'creditCardHolderInfo' => new CreditCardHolderInfo(
                'John Doe',
                'john@example.com',
                '12345678901',
                '12345678',
                '123',
                'Apt 1',
                '1234567890',
                '1234567890'
            ),
            'remoteIp' => '127.0.0.1'
        ];

        $this->validHolderInfo = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'cpfCnpj' => '12345678901',
            'postalCode' => '12345678',
            'addressNumber' => '123',
            'addressComplement' => 'Apt 1',
            'phone' => '1234567890',
            'mobilePhone' => '1234567890'
        ];
    }

    public function testTokenizeCreditCardWithValidData(): void
    {
        $expectedResponse = [
            'customer' => 'cus_123',
            'creditCardNumber' => '1111',
            'creditCardBrand' => 'VISA',
            'creditCardToken' => 'token_123'
        ];

        $this->clientMock
            ->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo('creditCard/tokenizeCreditCard'),
                $this->callback(function ($data) {
                    return
                        isset($data['customer']) &&
                        isset($data['creditCard']) &&
                        isset($data['creditCardHolderInfo']) &&
                        isset($data['remoteIp']);
                })
            )
            ->willReturn($expectedResponse);

        $result = $this->repository->tokenizeCreditCard($this->validCreditCardData);

        $this->assertInstanceOf(CreditCard::class, $result);
        $this->assertEquals('1111', $result->getCreditCardNumber());
        $this->assertEquals('VISA', $result->getCreditCardBrand());
        $this->assertEquals('token_123', $result->getCreditCardToken());
    }

    public function testTokenizeCreditCardWithValidResponseData(): void
    {
        $expectedResponse = [
            'customer' => 'cus_123',
            'creditCardNumber' => '1111',
            'creditCardBrand' => 'MASTERCARD',
            'creditCardToken' => 'token_456',
            'creditCard' => [
                'number' => '1111',
                'holderName' => 'John Doe',
                'expiryMonth' => '12',
                'expiryYear' => '2025',
                'creditCardBrand' => 'MASTERCARD'
            ],
            'creditCardHolderInfo' => $this->validHolderInfo
        ];

        $this->clientMock
            ->expects($this->once())
            ->method('post')
            ->willReturn($expectedResponse);

        $result = $this->repository->tokenizeCreditCard($this->validCreditCardData);

        $this->assertInstanceOf(CreditCard::class, $result);
        $this->assertEquals('1111', $result->getCreditCardNumber());
        $this->assertEquals('MASTERCARD', $result->getCreditCardBrand());
        $this->assertEquals('token_456', $result->getCreditCardToken());
        $this->assertEquals('cus_123', $result->getCustomer());
    }

    public function testGetDefaultEndpoint(): void
    {
        $reflection = new \ReflectionClass($this->repository);
        $method = $reflection->getMethod('getDefaultEndpoint');
        $method->setAccessible(true);

        $result = $method->invoke($this->repository);

        $this->assertEquals('creditCard', $result);
    }

    public function testPrepareSendDataFormatsDataCorrectly(): void
    {
        $reflection = new \ReflectionClass($this->repository);
        $method = $reflection->getMethod('prepareSendData');
        $method->setAccessible(true);

        $result = $method->invoke($this->repository, $this->validCreditCardData);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('customer', $result);
        $this->assertArrayHasKey('creditCard', $result);
        $this->assertArrayHasKey('creditCardHolderInfo', $result);
        $this->assertArrayHasKey('remoteIp', $result);

        // Verifica se os objetos foram convertidos em arrays
        $this->assertIsArray($result['creditCard']);
        $this->assertIsArray($result['creditCardHolderInfo']);
    }
}
