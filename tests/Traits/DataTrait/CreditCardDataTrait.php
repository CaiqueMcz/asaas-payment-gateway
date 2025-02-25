<?php

namespace AsaasPaymentGateway\Tests\Traits\DataTrait;

use AsaasPaymentGateway\Model\CreditCard;
use AsaasPaymentGateway\Repository\CreditCardRepository;

trait CreditCardDataTrait
{
    public function getRandomData(): array
    {
        $data = [];
        $data['remoteIp'] = $this->faker->ipv4;
        $data['customer'] = getenv("ASAAS_DEFAULT_CUSTOMER");
        return array_merge($data, $this->generateCreditCardData());
    }

    public function getFieldInfos(): array
    {
        return [
            'customer' => ['get' => 'getCustomer', 'set' => 'setCustomer'],
            'remoteIp' => ['get' => 'getRemoteIp', 'set' => 'setRemoteIp'],
        ];
    }

    public function getModelClass(): string
    {
        return CreditCard::class;
    }

    public function getRepositoryClass(): string
    {
        return CreditCardRepository::class;
    }
}
