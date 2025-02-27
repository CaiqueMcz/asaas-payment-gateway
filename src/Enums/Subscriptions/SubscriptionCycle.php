<?php

namespace CaiqueMcz\AsaasPaymentGateway\Enums\Subscriptions;

use MyCLabs\Enum\Enum;

/**
 * SubscriptionCycle Enum - Ciclo de Assinatura
 *
 * @method static self WEEKLY()
 * @method static self BIWEEKLY()
 * @method static self MONTHLY()
 * @method static self BIMONTHLY()
 * @method static self QUARTERLY()
 * @method static self SEMIANNUALLY()
 * @method static self YEARLY()
 */
class SubscriptionCycle extends Enum
{
    private const WEEKLY = 'WEEKLY';

    private const BIWEEKLY = 'BIWEEKLY';

    private const MONTHLY = 'MONTHLY';

    private const BIMONTHLY = 'BIMONTHLY';
    private const QUARTERLY = 'QUARTERLY';

    private const SEMIANNUALLY = 'SEMIANNUALLY';

    private const YEARLY = 'YEARLY';
}
