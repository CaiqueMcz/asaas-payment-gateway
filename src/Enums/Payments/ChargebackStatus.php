<?php

namespace CaiqueMcz\AsaasPaymentGateway\Enums\Payments;

use MyCLabs\Enum\Enum;

/**
 * ChargebackStatus Enum - Status do Chargeback
 *
 * @method static self REQUESTED()
 * @method static self IN_DISPUTE()
 * @method static self DISPUTE_LOST()
 * @method static self REVERSED()
 * @method static self DONE()
 */
class ChargebackStatus extends Enum
{
    private const REQUESTED = 'REQUESTED';
    private const IN_DISPUTE = 'IN_DISPUTE';
    private const DISPUTE_LOST = 'DISPUTE_LOST';
    private const REVERSED = 'REVERSED';
    private const DONE = 'DONE';
}
