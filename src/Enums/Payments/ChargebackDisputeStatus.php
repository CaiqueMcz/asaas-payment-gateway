<?php

namespace AsaasPaymentGateway\Enums\Payments;

use MyCLabs\Enum\Enum;

class ChargebackDisputeStatus extends Enum
{
    private const REQUESTED = 'REQUESTED';
    private const ACCEPTED = 'ACCEPTED';
    private const REJECTED = 'REJECTED';
}
