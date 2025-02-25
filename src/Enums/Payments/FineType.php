<?php

namespace AsaasPaymentGateway\Enums\Payments;

use MyCLabs\Enum\Enum;

/**
 * FineType Enum
 *
 * @method static self FIXED()
 * @method static self PERCENTAGE()
 */
class FineType extends Enum
{
    private const FIXED = 'FIXED';
    private const PERCENTAGE = 'PERCENTAGE';
}
