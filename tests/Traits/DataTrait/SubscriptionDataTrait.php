<?php

namespace AsaasPaymentGateway\Tests\Traits\DataTrait;

use AsaasPaymentGateway\Enums\Payments\BillingType;
use AsaasPaymentGateway\Enums\Subscriptions\SubscriptionCycle;
use AsaasPaymentGateway\Enums\Subscriptions\SubscriptionStatus;
use AsaasPaymentGateway\Helpers\Utils;
use AsaasPaymentGateway\Model\Subscription;
use AsaasPaymentGateway\Repository\SubscriptionRepository;
use AsaasPaymentGateway\Tests\Traits\HasFieldInfo;
use AsaasPaymentGateway\ValueObject\Payments\SplitList;

/**
 * @property \Faker\Generator $faker
 */
trait SubscriptionDataTrait
{
    use HasFieldInfo;

    public array $basicAtributes = [
        'customer',
        'billingType',
        'value',
        'nextDueDate',
        'cycle',
        'description',
        'externalReference',
        'deleted'
    ];

    /**
     * @throws \Exception
     */
    public function getRandomData(): array
    {
        $splitList = new SplitList();
        $splitList->addSplit($this->getRandSplit());
        return [
            'customer' => getenv("ASAAS_DEFAULT_CUSTOMER"),
            'billingType' => BillingType::CREDIT_CARD(),
            'value' => Utils::generateRandomFloat(1000, 10000),
            'nextDueDate' => Utils::getDueDate(5),
            'cycle' => SubscriptionCycle::MONTHLY(),
            'status' => SubscriptionStatus::ACTIVE(),
            'description' => "Subscription #" . $this->faker->uuid(),
            'externalReference' => $this->faker->uuid(),
            'splits' => $splitList,
            'deleted' => false,
        ];
    }

    public function getModelClass(): string
    {
        return Subscription::class;
    }

    public function getRepositoryClass(): string
    {
        return SubscriptionRepository::class;
    }
}
