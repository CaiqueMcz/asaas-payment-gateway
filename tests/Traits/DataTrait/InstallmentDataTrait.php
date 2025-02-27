<?php

namespace CaiqueMcz\AsaasPaymentGateway\Tests\Traits\DataTrait;

use CaiqueMcz\AsaasPaymentGateway\Enums\Payments\BillingType;
use CaiqueMcz\AsaasPaymentGateway\Helpers\Utils;
use CaiqueMcz\AsaasPaymentGateway\Model\Installment;
use CaiqueMcz\AsaasPaymentGateway\Repository\InstallmentRepository;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\HasFieldInfo;
use CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments\SplitList;

/**
 * @property \Faker\Generator $faker
 */
trait InstallmentDataTrait
{
    use HasFieldInfo;

    public array $basicAtributes = [
        'installmentCount'
    ];

    public function getRandomData(): array
    {
        $splitList = new SplitList();
        $splitList->addSplit($this->getRandSplit());
        $data = [];
        $data['installmentCount'] = 2;
        $data['customer'] = getenv("ASAAS_DEFAULT_CUSTOMER");
        $data['value'] = 120;
        $data['billingType'] = BillingType::CREDIT_CARD();
        $data['dueDate'] = Utils::getDueDate();
        $data['description'] = "Installment #" . $this->faker->uuid();
        $data['paymentExternalReference'] = $this->faker->uuid();
        $data['splits'] = $splitList;
        return $data;
    }


    public function getModelClass(): string
    {
        return Installment::class;
    }

    public function getRepositoryClass(): string
    {
        return InstallmentRepository::class;
    }
}
