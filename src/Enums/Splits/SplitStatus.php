<?php

namespace AsaasPaymentGateway\Enums\Splits;

use MyCLabs\Enum\Enum;

/**
 * SplitStatus Enum - Status da Divisão
 *
 * @method static self PENDING()
 * @method static self AWAITING_CREDIT()
 * @method static self CANCELLED()
 * @method static self DONE()
 * @method static self REFUNDED()
 * @method static self BLOCKED_BY_VALUE_DIVERGENCE()
 */
class SplitStatus extends Enum
{
    private const PENDING = 'PENDING';
    private const AWAITING_CREDIT = 'AWAITING_CREDIT';
    private const CANCELLED = 'CANCELLED';
    private const DONE = 'DONE';
    private const REFUNDED = 'REFUNDED';
    private const BLOCKED_BY_VALUE_DIVERGENCE = 'BLOCKED_BY_VALUE_DIVERGENCE';
}
