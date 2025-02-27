<?php

namespace CaiqueMcz\AsaasPaymentGateway\Enums\Payments;

use MyCLabs\Enum\Enum;

/**
 * RefundStatus Enum - Status do Reembolso
 *
 * @method static self PENDING()
 * @method static self AWAITING_CRITICAL_ACTION_AUTHORIZATION()
 * @method static self AWAITING_CUSTOMER_EXTERNAL_AUTHORIZATION()
 * @method static self CANCELLED()
 * @method static self DONE()
 */
class RefundStatus extends Enum
{
    private const PENDING = 'PENDING';
    private const AWAITING_CRITICAL_ACTION_AUTHORIZATION = 'AWAITING_CRITICAL_ACTION_AUTHORIZATION';
    private const AWAITING_CUSTOMER_EXTERNAL_AUTHORIZATION = 'AWAITING_CUSTOMER_EXTERNAL_AUTHORIZATION';
    private const CANCELLED = 'CANCELLED';
    private const DONE = 'DONE';
}
