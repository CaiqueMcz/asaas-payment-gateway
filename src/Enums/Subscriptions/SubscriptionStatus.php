<?php

namespace AsaasPaymentGateway\Enums\Subscriptions;

use MyCLabs\Enum\Enum;

/**
 * SubscriptionStatus Enum - Status da Assinatura
 *
 * @method static self ACTIVE()
 * @method static self EXPIRED()
 * @method static self INACTIVE()
 */
class SubscriptionStatus extends Enum
{
    private const ACTIVE = 'ACTIVE';


    private const EXPIRED = 'EXPIRED';

    private const INACTIVE = 'INACTIVE';
}
