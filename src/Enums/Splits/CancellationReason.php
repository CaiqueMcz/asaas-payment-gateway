<?php

namespace CaiqueMcz\AsaasPaymentGateway\Enums\Splits;

use MyCLabs\Enum\Enum;

/**
 * CancellationReason Enum - Motivo do Cancelamento
 *
 * @method static self PAYMENT_DELETED()
 * @method static self PAYMENT_OVERDUE()
 * @method static self PAYMENT_RECEIVED_IN_CASH()
 * @method static self PAYMENT_REFUNDED()
 * @method static self VALUE_DIVERGENCE_BLOCK()
 * @method static self WALLET_UNABLE_TO_RECEIVE()
 */
class CancellationReason extends Enum
{
    private const PAYMENT_DELETED = 'PAYMENT_DELETED';
    private const PAYMENT_OVERDUE = 'PAYMENT_OVERDUE';
    private const PAYMENT_RECEIVED_IN_CASH = 'PAYMENT_RECEIVED_IN_CASH';
    private const PAYMENT_REFUNDED = 'PAYMENT_REFUNDED';
    private const VALUE_DIVERGENCE_BLOCK = 'VALUE_DIVERGENCE_BLOCK';
    private const WALLET_UNABLE_TO_RECEIVE = 'WALLET_UNABLE_TO_RECEIVE';
}
