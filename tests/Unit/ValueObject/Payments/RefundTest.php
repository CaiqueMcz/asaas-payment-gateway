<?php

namespace AsaasPaymentGateway\Tests\Unit\ValueObject\Payments;

use AsaasPaymentGateway\Enums\Payments\RefundStatus;
use AsaasPaymentGateway\ValueObject\Payments\Refund;
use PHPUnit\Framework\TestCase;

class RefundTest extends TestCase
{
    private array $completeData;
    private array $minimalData;

    public function testCreateFromArrayWithCompleteData(): void
    {
        $refund = Refund::fromArray($this->completeData);

        $this->assertInstanceOf(Refund::class, $refund);
        $this->assertEquals('2023-01-15', $refund->getDateCreated());
        $this->assertEquals('DONE', $refund->getStatus());
        $this->assertEquals(150.75, $refund->getValue());
        $this->assertEquals('end123', $refund->getEndToEndIdentifier());
        $this->assertEquals('Test refund', $refund->getDescription());
        $this->assertEquals('2023-01-16', $refund->getEffectiveDate());
        $this->assertEquals('https://example.com/receipt', $refund->getTransactionReceiptUrl());
        $this->assertCount(2, $refund->getRefundedSplits());
        $this->assertEquals('pay_123456789', $refund->getPaymentId());
    }

    public function testCreateFromArrayWithMinimalData(): void
    {
        $refund = Refund::fromArray($this->minimalData);

        $this->assertEquals('2023-01-15', $refund->getDateCreated());
        $this->assertEquals('PENDING', $refund->getStatus());
        $this->assertEquals(100.00, $refund->getValue());
        $this->assertNull($refund->getEndToEndIdentifier());
        $this->assertNull($refund->getDescription());
        $this->assertNull($refund->getEffectiveDate());
        $this->assertNull($refund->getTransactionReceiptUrl());
        $this->assertEmpty($refund->getRefundedSplits());
        $this->assertNull($refund->getPaymentId());
    }

    public function testToArrayWithCompleteData(): void
    {
        $refund = Refund::fromArray($this->completeData);
        $array = $refund->toArray();

        $this->assertIsArray($array);
        $this->assertEquals($this->completeData['dateCreated'], $array['dateCreated']);
        $this->assertEquals($this->completeData['status'], $array['status']);
        $this->assertEquals($this->completeData['value'], $array['value']);
        $this->assertEquals($this->completeData['endToEndIdentifier'], $array['endToEndIdentifier']);
        $this->assertEquals($this->completeData['description'], $array['description']);
        $this->assertEquals($this->completeData['effectiveDate'], $array['effectiveDate']);
        $this->assertEquals($this->completeData['transactionReceiptUrl'], $array['transactionReceiptUrl']);
        $this->assertEquals($this->completeData['refundedSplits'], $array['refundedSplits']);
        $this->assertEquals($this->completeData['paymentId'], $array['paymentId']);
    }

    public function testToArrayWithMinimalData(): void
    {
        $refund = Refund::fromArray($this->minimalData);
        $array = $refund->toArray();

        $this->assertIsArray($array);
        $this->assertEquals($this->minimalData['dateCreated'], $array['dateCreated']);
        $this->assertEquals($this->minimalData['value'], $array['value']);
        $this->assertNull($array['endToEndIdentifier']);
        $this->assertNull($array['description']);
        $this->assertNull($array['effectiveDate']);
        $this->assertNull($array['transactionReceiptUrl']);
        $this->assertEmpty($array['refundedSplits']);
        $this->assertNull($array['paymentId']);
    }

    public function testCreateFromEmptyArray(): void
    {
        $refund = Refund::fromArray([]);

        $this->assertEquals('', $refund->getDateCreated());
        $this->assertEquals('', $refund->getStatus());
        $this->assertEquals(0.0, $refund->getValue());
        $this->assertNull($refund->getEndToEndIdentifier());
        $this->assertNull($refund->getDescription());
        $this->assertNull($refund->getEffectiveDate());
        $this->assertNull($refund->getTransactionReceiptUrl());
        $this->assertEmpty($refund->getRefundedSplits());
        $this->assertNull($refund->getPaymentId());
    }

    public function testConstructor(): void
    {
        $refund = new Refund(
            '2023-02-01',
            RefundStatus::DONE(),
            200.50,
            'end456',
            'Manual refund',
            '2023-02-02',
            'https://example.com/receipt-2',
            [['id' => 'split_3', 'value' => 200.50]],
            'pay_987654321'
        );

        $this->assertEquals('2023-02-01', $refund->getDateCreated());
        $this->assertEquals(RefundStatus::DONE(), $refund->getStatus());
        $this->assertEquals(200.50, $refund->getValue());
        $this->assertEquals('end456', $refund->getEndToEndIdentifier());
        $this->assertEquals('Manual refund', $refund->getDescription());
        $this->assertEquals('2023-02-02', $refund->getEffectiveDate());
        $this->assertEquals('https://example.com/receipt-2', $refund->getTransactionReceiptUrl());
        $this->assertCount(1, $refund->getRefundedSplits());
        $this->assertEquals('pay_987654321', $refund->getPaymentId());
    }

    public function testDataTypeCasting(): void
    {
        $data = [
            'dateCreated' => 20230301,  // integer
            'value' => '299.99',        // string
            'paymentId' => 12345        // integer
        ];

        $refund = Refund::fromArray($data);

        $this->assertIsString($refund->getDateCreated());
        $this->assertEquals('20230301', $refund->getDateCreated());
        $this->assertIsFloat($refund->getValue());
        $this->assertEquals(299.99, $refund->getValue());

        $this->assertIsString($refund->getPaymentId());
        $this->assertEquals('12345', $refund->getPaymentId());
    }

    public function testPaymentIdOnlyWithConstructor(): void
    {
        $refund = new Refund(
            '2023-03-01',
            RefundStatus::PENDING(),
            75.50,
            null,
            null,
            null,
            null,
            [],
            'payment_only_test'
        );

        $this->assertEquals('payment_only_test', $refund->getPaymentId());
        $this->assertNull($refund->getEndToEndIdentifier());
    }

    protected function setUp(): void
    {
        $this->completeData = [
            'dateCreated' => '2023-01-15',
            'status' => (string)RefundStatus::DONE(),
            'value' => 150.75,
            'endToEndIdentifier' => 'end123',
            'description' => 'Test refund',
            'effectiveDate' => '2023-01-16',
            'transactionReceiptUrl' => 'https://example.com/receipt',
            'refundedSplits' => [
                ['id' => 'split_1', 'value' => 50.25],
                ['id' => 'split_2', 'value' => 100.50]
            ],
            'paymentId' => 'pay_123456789'
        ];

        $this->minimalData = [
            'dateCreated' => '2023-01-15',
            'status' => (string)RefundStatus::PENDING(),
            'value' => 100.00
        ];
    }
}
