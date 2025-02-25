<?php

namespace AsaasPaymentGateway\Model;

use AsaasPaymentGateway\Exception\AsaasException;
use AsaasPaymentGateway\ValueObject\Payments\CreditCard as CreditCardValueObject;
use AsaasPaymentGateway\ValueObject\Payments\CreditCardHolderInfo;

class CreditCard extends AbstractModel
{
    protected array $fields = [
        'customer',
        'creditCard',
        'creditCardHolderInfo',
        'creditCardToken',
        'remoteIp',
        'creditCardNumber',
        'creditCardBrand'
    ];

    protected array $casts = [
        'creditCard' => CreditCardValueObject::class,
        'creditCardHolderInfo' => CreditCardHolderInfo::class,
    ];

    /**
     * @throws AsaasException
     */
    public function tokenizeCreditCard(): ?AbstractModel
    {
        return static::getRepository()->tokenizeCreditCard($this->toArray());
    }
}
