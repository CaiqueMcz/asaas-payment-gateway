<?php

namespace CaiqueMcz\AsaasPaymentGateway\Tests\Traits\DataTrait;

use CaiqueMcz\AsaasPaymentGateway\Model\Customer;
use CaiqueMcz\AsaasPaymentGateway\Repository\CustomerRepository;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\HasFieldInfo;

trait CustomerDataTrait
{
    use HasFieldInfo;

    public array $basicAtributes = [
        'name',
        'cpfCnpj',
        'email',
        'notificationDisabled'
    ];

    public function getRandomData(): array
    {
        return [
            'name' => $this->faker->name,
            'cpfCnpj' => str_replace([",", ".", "-"], "", $this->faker->cpf),
            'email' => $this->faker->unique()->safeEmail,
            'notificationDisabled' => false,
            'deleted' => false
        ];
    }

    public function getModelClass(): string
    {
        return Customer::class;
    }

    public function getRepositoryClass(): string
    {
        return CustomerRepository::class;
    }
}
