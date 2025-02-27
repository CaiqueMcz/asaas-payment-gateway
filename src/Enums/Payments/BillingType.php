<?php

namespace CaiqueMcz\AsaasPaymentGateway\Enums\Payments;

use CaiqueMcz\AsaasPaymentGateway\Enums\Subscriptions\SubscriptionCycle;
use MyCLabs\Enum\Enum;

/**
 * BillingType Enum - Forma de pagamento
 *
 * @method static self UNDEFINED()
 * @method static self BOLETO()
 * @method static self CREDIT_CARD()
 * @method static self PIX()
 * @method static self DEPOSIT()
 */
class BillingType extends Enum
{
    private const UNDEFINED = 'UNDEFINED';
    private const BOLETO = 'BOLETO';
    private const CREDIT_CARD = 'CREDIT_CARD';
    private const PIX = 'PIX';
    private const DEPOSIT = 'DEPOSIT';
}
