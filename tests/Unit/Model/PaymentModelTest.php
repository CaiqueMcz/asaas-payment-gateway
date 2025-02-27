<?php

namespace CaiqueMcz\AsaasPaymentGateway\Tests\Unit\Model;

use CaiqueMcz\AsaasPaymentGateway\Enums\Payments\BillingType;
use CaiqueMcz\AsaasPaymentGateway\Enums\Payments\PaymentStatus;
use CaiqueMcz\AsaasPaymentGateway\Exception\AsaasException;
use CaiqueMcz\AsaasPaymentGateway\Helpers\CDate;
use CaiqueMcz\AsaasPaymentGateway\Model\Payment;
use PHPUnit\Framework\TestCase;

class PaymentModelTest extends TestCase
{
    private array $validPaymentData;

    /**
     * @throws AsaasException
     */
    public function testCreatePaymentWithMinimumRequiredFields(): void
    {
        $payment = new Payment($this->validPaymentData);

        $this->assertEquals('cus_123', $payment->getCustomer());
        $this->assertEquals(BillingType::CREDIT_CARD(), $payment->getBillingType());
        $this->assertEquals(100.00, $payment->getValue());
        $this->assertEquals(new CDate('2025-12-31'), $payment->getDueDate());
    }

    public function testCreatePaymentWithMissingRequiredFieldThrowsException(): void
    {
        $this->expectException(AsaasException::class);

        unset($this->validPaymentData['customer']);
        new Payment($this->validPaymentData);
    }

    /**
     * @throws AsaasException
     */
    public function testPaymentStatusTransitions(): void
    {
        $payment = new Payment($this->validPaymentData);

        $this->assertNull($payment->getStatus());

        $payment->setStatus(PaymentStatus::PENDING());
        $this->assertEquals(PaymentStatus::PENDING(), $payment->getStatus());

        $payment->setStatus(PaymentStatus::RECEIVED());
        $this->assertEquals(PaymentStatus::RECEIVED(), $payment->getStatus());
    }

    /**
     * @throws AsaasException
     */
    public function testUpdatePaymentValue(): void
    {
        $payment = new Payment($this->validPaymentData);

        $newValue = 150.00;
        $payment->setValue($newValue);

        $this->assertEquals($newValue, $payment->getValue());
    }

    public function testInvalidValueThrowsException(): void
    {
        $this->expectException(AsaasException::class);

        $payment = new Payment($this->validPaymentData);
        $payment->setValue('invalid');
    }

    protected function setUp(): void
    {
        $this->validPaymentData = [
            'customer' => 'cus_123',
            'billingType' => BillingType::CREDIT_CARD(),
            'value' => 100.00,
            'dueDate' => '2025-12-31'
        ];
    }
}
