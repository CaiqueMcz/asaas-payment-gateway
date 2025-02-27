<?php

namespace CaiqueMcz\AsaasPaymentGateway\Tests\Unit\ValueObject\Payments;

use CaiqueMcz\AsaasPaymentGateway\Exception\AsaasException;
use CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments\Refund;
use CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments\RefundList;
use PHPUnit\Framework\TestCase;

class RefundListTest extends TestCase
{
    private array $testData;

    protected function setUp(): void
    {
        $this->testData = [
            [
                'dateCreated' => '2023-01-15',
                'status' => 'DONE',
                'value' => 150.75,
                'description' => 'First refund',
                'paymentId' => 'pay_123456789'
            ],
            [
                'dateCreated' => '2023-02-20',
                'status' => 'PENDING',
                'value' => 200.50,
                'description' => 'Second refund',
                'paymentId' => 'pay_987654321'
            ]
        ];
    }

    public function testFromArrayCreatesRefundListWithValidData(): void
    {
        $refundList = RefundList::fromArray($this->testData);
        $refunds = $refundList->getRefunds();

        $this->assertCount(2, $refunds);
        $this->assertInstanceOf(Refund::class, $refunds[0]);
        $this->assertInstanceOf(Refund::class, $refunds[1]);

        $this->assertEquals('First refund', $refunds[0]->getDescription());
        $this->assertEquals('Second refund', $refunds[1]->getDescription());

        $this->assertEquals('pay_123456789', $refunds[0]->getPaymentId());
        $this->assertEquals('pay_987654321', $refunds[1]->getPaymentId());
    }

    public function testAddRefundAppendsRefundToList(): void
    {
        $refundList = new RefundList();
        $refund = Refund::fromArray($this->testData[0]);

        $refundList->addRefund($refund);
        $refunds = $refundList->getRefunds();

        $this->assertCount(1, $refunds);
        $this->assertEquals('First refund', $refunds[0]->getDescription());
    }

    public function testFromArrayWithEmptyArrayCreatesEmptyRefundList(): void
    {
        $refundList = RefundList::fromArray([]);

        $this->assertEmpty($refundList->getRefunds());
        $this->assertEquals(0, $refundList->count());
    }

    public function testMultipleAddRefundsAppendAllRefunds(): void
    {
        $refundList = new RefundList();

        foreach ($this->testData as $data) {
            $refundList->addRefund(Refund::fromArray($data));
        }

        $this->assertEquals(2, $refundList->count());
        $this->assertEquals('First refund', $refundList->getRefundAt(0)->getDescription());
        $this->assertEquals('Second refund', $refundList->getRefundAt(1)->getDescription());
    }

    public function testToArrayPreservesAllRefundData(): void
    {
        $refundList = RefundList::fromArray($this->testData);
        $array = $refundList->toArray();

        $this->assertIsArray($array);
        $this->assertCount(2, $array);

        $this->assertEquals($this->testData[0]['dateCreated'], $array[0]['dateCreated']);
        $this->assertEquals($this->testData[0]['status'], $array[0]['status']);
        $this->assertEquals($this->testData[0]['value'], $array[0]['value']);
        $this->assertEquals($this->testData[0]['description'], $array[0]['description']);
        $this->assertEquals($this->testData[0]['paymentId'], $array[0]['paymentId']);

        $this->assertEquals($this->testData[1]['dateCreated'], $array[1]['dateCreated']);
        $this->assertEquals($this->testData[1]['status'], $array[1]['status']);
        $this->assertEquals($this->testData[1]['value'], $array[1]['value']);
        $this->assertEquals($this->testData[1]['description'], $array[1]['description']);
        $this->assertEquals($this->testData[1]['paymentId'], $array[1]['paymentId']);
    }

    public function testGetRefundAtReturnsCorrectRefund(): void
    {
        $refundList = RefundList::fromArray($this->testData);

        $firstRefund = $refundList->getRefundAt(0);
        $secondRefund = $refundList->getRefundAt(1);

        $this->assertInstanceOf(Refund::class, $firstRefund);
        $this->assertInstanceOf(Refund::class, $secondRefund);

        $this->assertEquals('First refund', $firstRefund->getDescription());
        $this->assertEquals('Second refund', $secondRefund->getDescription());
    }

    public function testGetRefundAtReturnsNullForInvalidIndex(): void
    {
        $refundList = RefundList::fromArray($this->testData);

        $this->assertNull($refundList->getRefundAt(5));
        $this->assertNull($refundList->getRefundAt(-1));
    }

    public function testCountReturnsCorrectNumberOfRefunds(): void
    {
        $refundList = new RefundList();
        $this->assertEquals(0, $refundList->count());

        $refundList->addRefund(Refund::fromArray($this->testData[0]));
        $this->assertEquals(1, $refundList->count());

        $refundList->addRefund(Refund::fromArray($this->testData[1]));
        $this->assertEquals(2, $refundList->count());
    }
}
