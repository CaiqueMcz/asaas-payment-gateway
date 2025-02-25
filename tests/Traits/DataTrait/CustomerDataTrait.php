<?php

namespace AsaasPaymentGateway\Tests\Traits\DataTrait;

use AsaasPaymentGateway\Model\Customer;
use AsaasPaymentGateway\Repository\CustomerRepository;
use AsaasPaymentGateway\Tests\Traits\HasFieldInfo;

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
