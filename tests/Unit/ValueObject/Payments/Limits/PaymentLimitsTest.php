<?php

namespace AsaasPaymentGateway\Tests\Unit\ValueObject\Payments\Limits;

use AsaasPaymentGateway\ValueObject\Payments\Limits\PaymentCreationLimits;
use AsaasPaymentGateway\ValueObject\Payments\Limits\PaymentDailyLimits;
use AsaasPaymentGateway\ValueObject\Payments\Limits\PaymentLimitsResponse;
use PHPUnit\Framework\TestCase;

class PaymentLimitsTest extends TestCase
{
    private array $testData;

    protected function setUp(): void
    {
        $this->testData = [
            'object' => 'limits',
            'creation' => [
                'daily' => [
                    'used' => 5,
                    'limit' => 100,
                    'remaining' => 95
                ]
            ]
        ];
    }

    public function testPaymentDailyLimits(): void
    {
        $daily = PaymentDailyLimits::fromArray($this->testData['creation']['daily']);

        $this->assertInstanceOf(PaymentDailyLimits::class, $daily);
        $this->assertEquals(5, $daily->getUsed());
        $this->assertEquals(100, $daily->getLimit());
        $this->assertEquals(95, $daily->getRemaining());

        $array = $daily->toArray();
        $this->assertEquals($this->testData['creation']['daily'], $array);
    }

    public function testPaymentCreationLimits(): void
    {
        $creation = PaymentCreationLimits::fromArray($this->testData['creation']);

        $this->assertInstanceOf(PaymentCreationLimits::class, $creation);
        $this->assertInstanceOf(PaymentDailyLimits::class, $creation->getDaily());

        $array = $creation->toArray();
        $this->assertEquals($this->testData['creation'], $array);
    }

    public function testPaymentLimitsResponse(): void
    {
        $response = PaymentLimitsResponse::fromArray($this->testData);

        $this->assertInstanceOf(PaymentLimitsResponse::class, $response);
        $this->assertEquals('limits', $response->getObject());
        $this->assertInstanceOf(PaymentCreationLimits::class, $response->getCreation());

        $array = $response->toArray();
        $this->assertEquals($this->testData, $array);
    }

    public function testIncompleteData(): void
    {
        $response = PaymentLimitsResponse::fromArray([]);

        $this->assertEquals('limits', $response->getObject());
        $this->assertEquals(0, $response->getCreation()->getDaily()->getUsed());
        $this->assertEquals(0, $response->getCreation()->getDaily()->getLimit());
        $this->assertEquals(0, $response->getCreation()->getDaily()->getRemaining());
    }
}
