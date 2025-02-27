<?php

namespace CaiqueMcz\AsaasPaymentGateway\Tests\Traits\DataTrait;

use CaiqueMcz\AsaasPaymentGateway\Enums\Payments\BillingType;
use CaiqueMcz\AsaasPaymentGateway\Enums\Payments\DiscountType;
use CaiqueMcz\AsaasPaymentGateway\Helpers\Utils;
use CaiqueMcz\AsaasPaymentGateway\Model\Payment;
use CaiqueMcz\AsaasPaymentGateway\Repository\PaymentRepository;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\HasFieldInfo;
use CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments\Discount;

trait PaymentDataTrait
{
    use HasFieldInfo;

    public array $basicAtributes = [
        'customer',
        'billingType',
        'value',
        'dueDate',
        'externalReference',
        'description',
        'discount'
    ];

    /**
     * @throws \Exception
     */
    public function getRandomData(): array
    {
        $value = Utils::generateRandomFloat();
        $qtyDays = 7;
        return [
            'customer' => getenv("ASAAS_DEFAULT_CUSTOMER"),
            'billingType' => BillingType::UNDEFINED(),
            'value' => $value,
            'dueDate' => Utils::getDueDate($qtyDays),
            'externalReference' => $this->faker->uuid(),
            'description' => "Payment #" . $this->faker->uuid(),
            'discount' => new Discount(5, $qtyDays - 2, DiscountType::PERCENTAGE()),
            'deleted' => false,
        ];
    }


    public function getModelClass(): string
    {
        return Payment::class;
    }

    public function getRepositoryClass(): string
    {
        return PaymentRepository::class;
    }
}
