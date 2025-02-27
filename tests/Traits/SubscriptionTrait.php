<?php

namespace CaiqueMcz\AsaasPaymentGateway\Tests\Traits;

use CaiqueMcz\AsaasPaymentGateway\Enums\Subscriptions\SubscriptionCycle;
use CaiqueMcz\AsaasPaymentGateway\Enums\Subscriptions\SubscriptionStatus;
use CaiqueMcz\AsaasPaymentGateway\Exception\AsaasException;
use CaiqueMcz\AsaasPaymentGateway\Helpers\CDate;
use CaiqueMcz\AsaasPaymentGateway\Helpers\Utils;
use CaiqueMcz\AsaasPaymentGateway\Model\AbstractModel;
use CaiqueMcz\AsaasPaymentGateway\Model\Payment;
use CaiqueMcz\AsaasPaymentGateway\Model\Subscription;
use CaiqueMcz\AsaasPaymentGateway\Response\ListResponse;
use CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments\CreditCard;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Subscription Trait
 * @property \Faker\Generator $faker
 **/
trait SubscriptionTrait
{
    /**
     * @throws AsaasException
     * @throws GuzzleException
     * @throws \Exception
     */
    public function cannotCreateWithCreditCard(): void
    {
        $randomData = $this->getRandomData();
        $randomData = array_merge($randomData, $this->generateCreditCardData());
        //$randomData['remoteIp'] = $this->faker->ipv4;

        if ($this->withMock === true) {
            $response = Utils::getSubscriptionsJsonFile('post_create_with_creditcard.json');
            $this->addInterceptor('post', 'subscriptions/', $response);
        }
        $this->expectException(AsaasException::class);
        Subscription::createWithCreditCard($randomData);
    }

    /**
     * @throws AsaasException
     * @throws GuzzleException
     * @throws \Exception
     */
    public function createWithCreditCard(): Subscription
    {
        $randomData = $this->getRandomData();
        $randomData = array_merge($randomData, $this->generateCreditCardData());
        $randomData['remoteIp'] = $this->faker->ipv4;

        if ($this->withMock === true) {
            $response = Utils::getSubscriptionsJsonFile('post_create_with_creditcard.json');
            $this->addInterceptor('post', 'subscriptions/', $response);
        }
        $entity = Subscription::createWithCreditCard($randomData);
        $this->assertNotNull($entity);
        $this->assertInstanceOf(Subscription::class, $entity);
        $this->assertInstanceOf(CDate::class, $entity->getDateCreated());
        $this->assertEquals(SubscriptionStatus::ACTIVE(), $entity->getStatus());
        $this->assertEquals(SubscriptionCycle::MONTHLY(), $entity->getCycle());
        $this->assertInstanceOf(CDate::class, $entity->getNextDueDate());
        $this->assertInstanceOf(CreditCard::class, $entity->getCreditCard());
        return $entity;
    }

    /**
     * @throws AsaasException
     * @throws GuzzleException
     * @throws \Exception
     */
    public function createWithCreditCardTokenized(Subscription $prevSubscription): Subscription
    {
        $randomData = $this->getRandomData();
        $randomData['remoteIp'] = $this->faker->ipv4;
        $randomData['creditCardToken'] = $prevSubscription->getCreditCard()->getCreditCardToken();

        if ($this->withMock === true) {
            $response = Utils::getSubscriptionsJsonFile('post_create_with_creditcard_tokenized.json');
            $this->addInterceptor('post', 'subscriptions/', $response);
        }

        $entity = Subscription::createWithCreditCardTokenized($randomData);
        $this->assertNotNull($entity);
        $this->assertInstanceOf(Subscription::class, $entity);
        $this->assertInstanceOf(CDate::class, $entity->getDateCreated());
        $this->assertEquals(SubscriptionStatus::ACTIVE(), $entity->getStatus());
        $this->assertEquals(SubscriptionCycle::MONTHLY(), $entity->getCycle());
        $this->assertInstanceOf(CDate::class, $entity->getNextDueDate());

        return $entity;
    }

    /**
     * @throws AsaasException
     */
    public function processGetPayments(AbstractModel $entity): AbstractModel
    {
        if ($this->withMock === true) {
            $expectedResponse = [
                'object' => 'list',
                'hasMore' => false,
                'totalCount' => 2,
                'limit' => 10,
                'offset' => 0,
                'data' => [
                    [
                        'id' => 'pay_' . $this->faker->uuid,
                        'customer' => $entity->getCustomer(),
                        'subscription' => $entity->getId(),
                        'value' => $entity->getValue(),
                        'billingType' => $entity->getBillingType(),
                        'dueDate' => date('Y-m-d')
                    ],
                    [
                        'id' => 'pay_' . $this->faker->uuid,
                        'customer' => $entity->getCustomer(),
                        'subscription' => $entity->getId(),
                        'value' => $entity->getValue(),
                        'billingType' => $entity->getBillingType(),
                        'dueDate' => date('Y-m-d', strtotime('+1 month'))
                    ]
                ]
            ];

            $this->addInterceptor("get", "subscriptions/" . $entity->getId() . "/payments", $expectedResponse);
        }

        $response = $entity->getPayments();
        $this->assertInstanceOf(ListResponse::class, $response);

        if ($response->getTotalCount() > 0) {
            foreach ($response->getRows() as $row) {
                $this->assertInstanceOf(Payment::class, $row);
            }
        }

        return $entity;
    }

    /**
     * @throws AsaasException
     */
    public function processGetPaymentBook(AbstractModel $entity): AbstractModel
    {
        if ($this->withMock === true) {
            $expectedResponse = ["https://www.asaas.com/paymentBook?id=" . $entity->getId()];
            $this->addInterceptor("get", "subscriptions/" . $entity->getId() . "/paymentBook", $expectedResponse);
        }

        $response = $entity->getPaymentBook();
        $this->assertNotEmpty($response);
        $this->assertStringContainsString('paymentBook', $response);

        return $entity;
    }
}
