<?php

namespace AsaasPaymentGateway\Tests\Unit\Model;

use AsaasPaymentGateway\Enums\Payments\BillingType;
use AsaasPaymentGateway\Enums\Payments\DiscountType;
use AsaasPaymentGateway\Enums\Payments\FineType;
use AsaasPaymentGateway\Enums\Subscriptions\SubscriptionCycle;
use AsaasPaymentGateway\Enums\Subscriptions\SubscriptionStatus;
use AsaasPaymentGateway\Exception\AsaasException;
use AsaasPaymentGateway\Helpers\CDate;
use AsaasPaymentGateway\Model\Subscription;
use AsaasPaymentGateway\Repository\SubscriptionRepository;
use AsaasPaymentGateway\Response\ListResponse;
use AsaasPaymentGateway\ValueObject\Payments\Discount;
use AsaasPaymentGateway\ValueObject\Payments\Fine;
use AsaasPaymentGateway\ValueObject\Payments\Interest;
use AsaasPaymentGateway\ValueObject\Payments\SplitList;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;

class SubscriptionModelTest extends TestCase
{
    private array $validData;

    /**
     * @throws AsaasException
     */
    public function testCreateSubscriptionWithMinimumRequiredFields(): void
    {
        $subscription = new Subscription($this->validData);

        $this->assertEquals('cus_123', $subscription->getCustomer());
        $this->assertEquals(BillingType::CREDIT_CARD(), $subscription->getBillingType());
        $this->assertEquals(100.00, $subscription->getValue());
        $this->assertEquals(CDate::from('2025-01-01'), $subscription->getNextDueDate());
        $this->assertEquals(SubscriptionCycle::MONTHLY(), $subscription->getCycle());
        $this->assertEquals('Test subscription', $subscription->getDescription());
    }

    public function testCreateSubscriptionWithMissingRequiredFieldThrowsException(): void
    {
        $this->expectException(AsaasException::class);

        unset($this->validData['customer']);
        new Subscription($this->validData);
    }

    /**
     * @throws AsaasException
     */
    public function testSettersAndGetters(): void
    {
        $subscription = new Subscription($this->validData);

        // Set optional fields
        $subscription->setId('sub_123');
        $subscription->setExternalReference('EXT123');
        $subscription->setEndDate(CDate::from('2026-01-01'));
        $subscription->setMaxPayments(12);
        $subscription->setStatus(SubscriptionStatus::ACTIVE());
        $subscription->setIsDeleted(false);

        // Test getters
        $this->assertEquals('sub_123', $subscription->getId());
        $this->assertEquals('EXT123', $subscription->getExternalReference());
        $this->assertEquals(CDate::from('2026-01-01'), $subscription->getEndDate());
        $this->assertEquals(12, $subscription->getMaxPayments());
        $this->assertEquals(SubscriptionStatus::ACTIVE(), $subscription->getStatus());
        $this->assertFalse($subscription->isDeleted());
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

        $subscription = new Subscription($data);

        $this->assertInstanceOf(Discount::class, $subscription->getDiscount());
        $this->assertInstanceOf(Interest::class, $subscription->getInterest());
        $this->assertInstanceOf(Fine::class, $subscription->getFine());
        $this->assertInstanceOf(SplitList::class, $subscription->getSplits());
    }

    /**
     * @throws AsaasException
     */
    public function testToArray(): void
    {
        $subscription = new Subscription($this->validData);
        $subscription->setId('sub_123');

        $array = $subscription->toArray();

        $this->assertIsArray($array);
        $this->assertEquals('sub_123', $array['id']);
        $this->assertEquals('cus_123', $array['customer']);
        $this->assertEquals((string)BillingType::CREDIT_CARD(), $array['billingType']);
        $this->assertEquals(100.00, $array['value']);
        $this->assertEquals((string)$subscription->getNextDueDate(), $array['nextDueDate']);
        $this->assertEquals((string)SubscriptionCycle::MONTHLY(), $array['cycle']);
        $this->assertEquals('Test subscription', $array['description']);
    }

    /**
     * @throws AsaasException
     */
    public function testGetPaymentsCallsRepository(): void
    {
        $mockRepository = $this->createMock(SubscriptionRepository::class);
        $mockResponse = $this->createMock(ListResponse::class);

        $mockRepository->expects($this->once())
            ->method('getPayments')
            ->with('sub_123', [])
            ->willReturn($mockResponse);

        Subscription::injectRepository(Subscription::class, $mockRepository);

        $subscription = new Subscription(array_merge(['id' => 'sub_123'], $this->validData));
        $result = $subscription->getPayments();

        $this->assertSame($mockResponse, $result);

        // Clean up
        Subscription::resetRepository(Subscription::class);
    }

    /**
     * @throws AsaasException
     */
    public function testGetPaymentBookCallsRepository(): void
    {
        $mockRepository = $this->createMock(SubscriptionRepository::class);

        $mockRepository->expects($this->once())
            ->method('getPaymentBook')
            ->with('sub_123', null, 'asc')
            ->willReturn('https://example.com/book');

        Subscription::injectRepository(Subscription::class, $mockRepository);

        $subscription = new Subscription(array_merge(['id' => 'sub_123'], $this->validData));
        $result = $subscription->getPaymentBook(null, 'asc');

        $this->assertEquals('https://example.com/book', $result);

        // Clean up
        Subscription::resetRepository(Subscription::class);
    }

    /**
     * @throws GuzzleException
     */
    public function testCreateWithCreditCardRequiresRequiredFields(): void
    {
        $this->expectException(AsaasException::class);

        $data = $this->validData;
        // Missing required remoteIp field
        Subscription::createWithCreditCard($data);
    }

    /**
     * @throws GuzzleException
     */
    public function testCreateWithCreditCardTokenizedRequiresRequiredFields(): void
    {
        $this->expectException(AsaasException::class);

        $data = $this->validData;
        // Missing required creditCardToken field
        Subscription::createWithCreditCardTokenized($data);
    }

    protected function setUp(): void
    {
        $this->validData = [
            'customer' => 'cus_123',
            'billingType' => BillingType::CREDIT_CARD(),
            'value' => 100.00,
            'nextDueDate' => CDate::from('2025-01-01'),
            'cycle' => SubscriptionCycle::MONTHLY(),
            'description' => 'Test subscription'
        ];
    }
}
