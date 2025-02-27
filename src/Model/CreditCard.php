<?php

namespace CaiqueMcz\AsaasPaymentGateway\Model;

use CaiqueMcz\AsaasPaymentGateway\Exception\AsaasException;
use CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments\CreditCard as CreditCardValueObject;
use CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments\CreditCardHolderInfo;

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
