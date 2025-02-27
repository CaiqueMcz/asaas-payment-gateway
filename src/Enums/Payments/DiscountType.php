<?php

namespace CaiqueMcz\AsaasPaymentGateway\Enums\Payments;

use MyCLabs\Enum\Enum;

/**
 * DiscountType Enum - Tipo de Desconto
 *
 * @method static self FIXED()
 * @method static self PERCENTAGE()
 */
class DiscountType extends Enum
{
    private const FIXED = 'FIXED';
    private const PERCENTAGE = 'PERCENTAGE';
}
