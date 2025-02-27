<?php

namespace CaiqueMcz\AsaasPaymentGateway\Tests\Unit\ValueObject\Payments;

use CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments\CreditCard;
use PHPUnit\Framework\TestCase;

class CreditCardValueObjectTest extends TestCase
{
    private array $testData;

    protected function setUp(): void
    {
        $this->testData = [
            'holderName' => 'John Doe',
            'number' => '4111111111111111',
            'expiryMonth' => '12',
            'expiryYear' => '2030',
            'ccv' => '123',
            'creditCardBrand' => 'VISA',
            'creditCardToken' => 'token_123456789'
        ];
    }

    public function testCreateFromArray(): void
    {
        $creditCard = CreditCard::fromArray($this->testData);

        $this->assertInstanceOf(CreditCard::class, $creditCard);
        $this->assertEquals('John Doe', $creditCard->getHolderName());
        $this->assertEquals('4111111111111111', $creditCard->getNumber());
        $this->assertEquals('12', $creditCard->getExpiryMonth());
        $this->assertEquals('2030', $creditCard->getExpiryYear());
        $this->assertEquals('123', $creditCard->getCcv());
        $this->assertEquals('VISA', $creditCard->getCreditCardBrand());
        $this->assertEquals('token_123456789', $creditCard->getCreditCardToken());
    }

    public function testToArray(): void
    {
        $creditCard = CreditCard::fromArray($this->testData);
        $array = $creditCard->toArray();

        $this->assertIsArray($array);
        $this->assertEquals($this->testData['holderName'], $array['holderName']);
        $this->assertEquals($this->testData['number'], $array['number']);
        $this->assertEquals($this->testData['expiryMonth'], $array['expiryMonth']);
        $this->assertEquals($this->testData['expiryYear'], $array['expiryYear']);
        $this->assertEquals($this->testData['ccv'], $array['ccv']);
        $this->assertEquals($this->testData['creditCardBrand'], $array['creditCardBrand']);
        $this->assertEquals($this->testData['creditCardToken'], $array['creditCardToken']);
    }

    public function testCreateWithMinimalData(): void
    {
        $minimalData = [
            'number' => '4111111111111111'
        ];

        $creditCard = CreditCard::fromArray($minimalData);

        $this->assertEquals('4111111111111111', $creditCard->getNumber());
        $this->assertNull($creditCard->getHolderName());
        $this->assertNull($creditCard->getExpiryMonth());
        $this->assertNull($creditCard->getExpiryYear());
        $this->assertNull($creditCard->getCcv());
        $this->assertNull($creditCard->getCreditCardBrand());
        $this->assertNull($creditCard->getCreditCardToken());
    }

    public function testCreateWithCreditCardNumberField(): void
    {
        $alternativeData = [
            'creditCardNumber' => '4111111111111111',
            'holderName' => 'Jane Doe'
        ];

        $creditCard = CreditCard::fromArray($alternativeData);

        $this->assertEquals('4111111111111111', $creditCard->getNumber());
        $this->assertEquals('Jane Doe', $creditCard->getHolderName());
    }

    public function testCreateWithEmptyDataUsesEmptyString(): void
    {
        $creditCard = CreditCard::fromArray([]);

        $this->assertEquals('', $creditCard->getNumber());
        $this->assertNull($creditCard->getHolderName());
    }

    public function testConstructorDirectly(): void
    {
        $creditCard = new CreditCard(
            '5555555555554444',
            'Jane Smith',
            '01',
            '2025',
            '321',
            'MASTERCARD',
            null
        );

        $this->assertEquals('5555555555554444', $creditCard->getNumber());
        $this->assertEquals('Jane Smith', $creditCard->getHolderName());
        $this->assertEquals('01', $creditCard->getExpiryMonth());
        $this->assertEquals('2025', $creditCard->getExpiryYear());
        $this->assertEquals('321', $creditCard->getCcv());
        $this->assertEquals('MASTERCARD', $creditCard->getCreditCardBrand());
        $this->assertNull($creditCard->getCreditCardToken());
    }

    public function testGetLastNumbers(): void
    {
        $creditCard = CreditCard::fromArray([
            'number' => '4111111111111111'
        ]);

        $this->assertEquals('1111', $creditCard->getLastNumbers());
    }

    public function testGetLastNumbersWithShortNumber(): void
    {
        $creditCard = CreditCard::fromArray([
            'number' => '123'
        ]);

        $this->assertEquals('123', $creditCard->getLastNumbers());
    }

    public function testPriorityOfNumberField(): void
    {
        $conflictingData = [
            'number' => '4111111111111111',
            'creditCardNumber' => '5555555555554444'
        ];

        $creditCard = CreditCard::fromArray($conflictingData);

        // number should be preferred over creditCardNumber
        $this->assertEquals('4111111111111111', $creditCard->getNumber());
    }
}
