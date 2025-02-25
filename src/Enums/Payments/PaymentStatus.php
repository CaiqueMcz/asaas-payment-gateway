<?php

namespace AsaasPaymentGateway\Enums\Payments;

use MyCLabs\Enum\Enum;

/**
 * PaymentStatus Enum - Status de Pagamento
 *
 * @method static self PENDING()
 * @method static self AUTHORIZED()
 * @method static self RECEIVED()
 * @method static self CONFIRMED()
 * @method static self OVERDUE()
 * @method static self REFUNDED()
 * @method static self RECEIVED_IN_CASH()
 * @method static self REFUND_REQUESTED()
 * @method static self REFUND_IN_PROGRESS()
 * @method static self CHARGEBACK_REQUESTED()
 * @method static self CHARGEBACK_DISPUTE()
 * @method static self AWAITING_CHARGEBACK_REVERSAL()
 * @method static self DUNNING_REQUESTED()
 * @method static self DUNNING_RECEIVED()
 * @method static self AWAITING_RISK_ANALYSIS()
 */
class PaymentStatus extends Enum
{
    private const PENDING = 'PENDING';
    private const AUTHORIZED = 'AUTHORIZED';
    private const RECEIVED = 'RECEIVED';
    private const CONFIRMED = 'CONFIRMED';
    private const OVERDUE = 'OVERDUE';
    private const REFUNDED = 'REFUNDED';
    private const RECEIVED_IN_CASH = 'RECEIVED_IN_CASH';
    private const REFUND_REQUESTED = 'REFUND_REQUESTED';
    private const REFUND_IN_PROGRESS = 'REFUND_IN_PROGRESS';
    private const CHARGEBACK_REQUESTED = 'CHARGEBACK_REQUESTED';
    private const CHARGEBACK_DISPUTE = 'CHARGEBACK_DISPUTE';
    private const AWAITING_CHARGEBACK_REVERSAL = 'AWAITING_CHARGEBACK_REVERSAL';
    private const DUNNING_REQUESTED = 'DUNNING_REQUESTED';
    private const DUNNING_RECEIVED = 'DUNNING_RECEIVED';
    private const AWAITING_RISK_ANALYSIS = 'AWAITING_RISK_ANALYSIS';
}
