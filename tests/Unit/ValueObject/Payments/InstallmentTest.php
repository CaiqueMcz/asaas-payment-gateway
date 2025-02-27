<?php

namespace CaiqueMcz\AsaasPaymentGateway\Tests\Unit\ValueObject\Payments;

use CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments\Installment;
use PHPUnit\Framework\TestCase;

class InstallmentTest extends TestCase
{
    private array $sampleData;

    protected function setUp(): void
    {
        $this->sampleData = [
            'paymentNetValue' => 95.50,
            'paymentValue' => 100.00
        ];
    }

    public function testCreateFromConstructor(): void
    {
        $installment = new Installment(95.50, 100.00);

        $this->assertEquals(95.50, $installment->getPaymentNetValue());
        $this->assertEquals(100.00, $installment->getPaymentValue());
    }

    public function testCreateFromArray(): void
    {
        $installment = Installment::fromArray($this->sampleData);

        $this->assertEquals($this->sampleData['paymentNetValue'], $installment->getPaymentNetValue());
        $this->assertEquals($this->sampleData['paymentValue'], $installment->getPaymentValue());
    }

    public function testToArray(): void
    {
        $installment = new Installment(95.50, 100.00);
        $array = $installment->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('paymentNetValue', $array);
        $this->assertArrayHasKey('paymentValue', $array);
        $this->assertEquals(95.50, $array['paymentNetValue']);
        $this->assertEquals(100.00, $array['paymentValue']);
    }

    public function testValueTypeConversion(): void
    {
        $data = [
            'paymentNetValue' => '95.50',
            'paymentValue' => '100.00'
        ];

        $installment = Installment::fromArray($data);

        $this->assertIsFloat($installment->getPaymentNetValue());
        $this->assertIsFloat($installment->getPaymentValue());
        $this->assertEquals(95.50, $installment->getPaymentNetValue());
        $this->assertEquals(100.00, $installment->getPaymentValue());
    }
}
