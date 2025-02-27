<?php

namespace CaiqueMcz\AsaasPaymentGateway\Tests\Traits\DataTrait;

use CaiqueMcz\AsaasPaymentGateway\Enums\Payments\BillingType;
use CaiqueMcz\AsaasPaymentGateway\Enums\Subscriptions\SubscriptionCycle;
use CaiqueMcz\AsaasPaymentGateway\Enums\Subscriptions\SubscriptionStatus;
use CaiqueMcz\AsaasPaymentGateway\Helpers\Utils;
use CaiqueMcz\AsaasPaymentGateway\Model\Subscription;
use CaiqueMcz\AsaasPaymentGateway\Repository\SubscriptionRepository;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\HasFieldInfo;
use CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments\SplitList;

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
