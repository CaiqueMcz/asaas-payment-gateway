<?php

namespace CaiqueMcz\AsaasPaymentGateway\Tests\Unit\Model;

use CaiqueMcz\AsaasPaymentGateway\Enums\Payments\DiscountType;
use CaiqueMcz\AsaasPaymentGateway\Enums\Payments\FineType;
use CaiqueMcz\AsaasPaymentGateway\Exception\AsaasException;
use CaiqueMcz\AsaasPaymentGateway\Model\Installment;
use CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments\Discount;
use CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments\Fine;
use CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments\Interest;
use CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments\SplitList;
use PHPUnit\Framework\TestCase;

class InstallmentTest extends TestCase
{
    private array $validData;

    /**
     * @throws AsaasException
     */
    public function testCreateInstallment(): void
    {
        $installment = new Installment($this->validData);

        $this->assertEquals(12, $installment->getInstallmentCount());
        $this->assertEquals('cus_123', $installment->getCustomer());
        $this->assertEquals(100.00, $installment->getValue());
        $this->assertEquals(1200.00, $installment->getTotalValue());
        $this->assertEquals('CREDIT_CARD', $installment->getBillingType());
        $this->assertEquals('2025-12-31', $installment->getDueDate());
        $this->assertEquals('Test installment', $installment->getDescription());
    }

    /**
     * @throws AsaasException
     */
    public function testCreateWithValueObjects(): void
    {
        $data = $this->validData;
        $data['discount'] = new Discount(10, 5, DiscountType::FIXED());
        $data['interest'] = new Interest(2.5);
        $data['fine'] = new Fine(5.0, FineType::PERCENTAGE());
        $data['splits'] = new SplitList();

        $installment = new Installment($data);

        $this->assertInstanceOf(Discount::class, $installment->getDiscount());
        $this->assertInstanceOf(Interest::class, $installment->getInterest());
        $this->assertInstanceOf(Fine::class, $installment->getFine());
        $this->assertInstanceOf(SplitList::class, $installment->getSplits());
    }

    public function testMissingRequiredFieldThrowsException(): void
    {
        $this->expectException(AsaasException::class);

        unset($this->validData['customer']);
        new Installment($this->validData);
    }

    public function testInvalidInstallmentCountTypeThrowsException(): void
    {
        $this->expectException(AsaasException::class);

        $this->validData['installmentCount'] = 'invalid';
        new Installment($this->validData);
    }

    public function testInvalidValueTypeThrowsException(): void
    {
        $this->expectException(AsaasException::class);

        $this->validData['value'] = 'invalid';
        new Installment($this->validData);
    }

    /**
     * @throws AsaasException
     */
    public function testToArray(): void
    {
        $installment = new Installment($this->validData);
        $array = $installment->toArray();

        $this->assertIsArray($array);
        $this->assertEquals(12, $array['installmentCount']);
        $this->assertEquals('cus_123', $array['customer']);
        $this->assertEquals(100.00, $array['value']);
        $this->assertEquals(1200.00, $array['totalValue']);
        $this->assertEquals('CREDIT_CARD', $array['billingType']);
        $this->assertEquals('2025-12-31', $array['dueDate']);
        $this->assertEquals('Test installment', $array['description']);
    }

    /**
     * @throws AsaasException
     */
    public function testBooleanAndIntegerCasts(): void
    {
        $data = $this->validData;
        $data['postalService'] = true;
        $data['daysAfterDueDateToRegistrationCancellation'] = 5;

        $installment = new Installment($data);

        $this->assertTrue($installment->isPostalService());
        $this->assertEquals(5, $installment->getDaysAfterDueDateToRegistrationCancellation());
    }

    protected function setUp(): void
    {
        $this->validData = [
            'installmentCount' => 12,
            'customer' => 'cus_123',
            'value' => 100.00,
            'totalValue' => 1200.00,
            'billingType' => 'CREDIT_CARD',
            'dueDate' => '2025-12-31',
            'description' => 'Test installment'
        ];
    }
}
