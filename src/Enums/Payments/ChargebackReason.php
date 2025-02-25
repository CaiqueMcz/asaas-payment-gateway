<?php

namespace AsaasPaymentGateway\Enums\Payments;

use MyCLabs\Enum\Enum;

/**
 * ChargebackReason Enum - Motivo do Chargeback
 *
 * @method static self ABSENCE_OF_PRINT()
 * @method static self ABSENT_CARD_FRAUD()
 * @method static self CARD_ACTIVATED_PHONE_TRANSACTION()
 * @method static self CARD_FRAUD()
 * @method static self CARD_RECOVERY_BULLETIN()
 * @method static self COMMERCIAL_DISAGREEMENT()
 * @method static self COPY_NOT_RECEIVED()
 * @method static self CREDIT_OR_DEBIT_PRESENTATION_ERROR()
 * @method static self DIFFERENT_PAY_METHOD()
 * @method static self FRAUD()
 * @method static self INCORRECT_TRANSACTION_VALUE()
 * @method static self INVALID_CURRENCY()
 * @method static self INVALID_DATA()
 * @method static self LATE_PRESENTATION()
 * @method static self LOCAL_REGULATORY_OR_LEGAL_DISPUTE()
 * @method static self MULTIPLE_ROCS()
 * @method static self ORIGINAL_CREDIT_TRANSACTION_NOT_ACCEPTED()
 * @method static self OTHER_ABSENT_CARD_FRAUD()
 * @method static self PROCESS_ERROR()
 * @method static self RECEIVED_COPY_ILLEGIBLE_OR_INCOMPLETE()
 * @method static self RECURRENCE_CANCELED()
 * @method static self REQUIRED_AUTHORIZATION_NOT_GRANTED()
 * @method static self RIGHT_OF_FULL_RECOURSE_FOR_FRAUD()
 * @method static self SALE_CANCELED()
 * @method static self SERVICE_DISAGREEMENT_OR_DEFECTIVE_PRODUCT()
 * @method static self SERVICE_NOT_RECEIVED()
 * @method static self SPLIT_SALE()
 * @method static self TRANSFERS_OF_DIVERSE_RESPONSIBILITIES()
 * @method static self UNQUALIFIED_CAR_RENTAL_DEBIT()
 * @method static self USA_CARDHOLDER_DISPUTE()
 * @method static self VISA_FRAUD_MONITORING_PROGRAM()
 * @method static self WARNING_BULLETIN_FILE()
 */
class ChargebackReason extends Enum
{
    private const ABSENCE_OF_PRINT = 'ABSENCE_OF_PRINT';
    private const ABSENT_CARD_FRAUD = 'ABSENT_CARD_FRAUD';
    private const CARD_ACTIVATED_PHONE_TRANSACTION = 'CARD_ACTIVATED_PHONE_TRANSACTION';
    private const CARD_FRAUD = 'CARD_FRAUD';
    private const CARD_RECOVERY_BULLETIN = 'CARD_RECOVERY_BULLETIN';
    private const COMMERCIAL_DISAGREEMENT = 'COMMERCIAL_DISAGREEMENT';
    private const COPY_NOT_RECEIVED = 'COPY_NOT_RECEIVED';
    private const CREDIT_OR_DEBIT_PRESENTATION_ERROR = 'CREDIT_OR_DEBIT_PRESENTATION_ERROR';
    private const DIFFERENT_PAY_METHOD = 'DIFFERENT_PAY_METHOD';
    private const FRAUD = 'FRAUD';
    private const INCORRECT_TRANSACTION_VALUE = 'INCORRECT_TRANSACTION_VALUE';
    private const INVALID_CURRENCY = 'INVALID_CURRENCY';
    private const INVALID_DATA = 'INVALID_DATA';
    private const LATE_PRESENTATION = 'LATE_PRESENTATION';
    private const LOCAL_REGULATORY_OR_LEGAL_DISPUTE = 'LOCAL_REGULATORY_OR_LEGAL_DISPUTE';
    private const MULTIPLE_ROCS = 'MULTIPLE_ROCS';
    private const ORIGINAL_CREDIT_TRANSACTION_NOT_ACCEPTED = 'ORIGINAL_CREDIT_TRANSACTION_NOT_ACCEPTED';
    private const OTHER_ABSENT_CARD_FRAUD = 'OTHER_ABSENT_CARD_FRAUD';
    private const PROCESS_ERROR = 'PROCESS_ERROR';
    private const RECEIVED_COPY_ILLEGIBLE_OR_INCOMPLETE = 'RECEIVED_COPY_ILLEGIBLE_OR_INCOMPLETE';
    private const RECURRENCE_CANCELED = 'RECURRENCE_CANCELED';
    private const REQUIRED_AUTHORIZATION_NOT_GRANTED = 'REQUIRED_AUTHORIZATION_NOT_GRANTED';
    private const RIGHT_OF_FULL_RECOURSE_FOR_FRAUD = 'RIGHT_OF_FULL_RECOURSE_FOR_FRAUD';
    private const SALE_CANCELED = 'SALE_CANCELED';
    private const SERVICE_DISAGREEMENT_OR_DEFECTIVE_PRODUCT = 'SERVICE_DISAGREEMENT_OR_DEFECTIVE_PRODUCT';
    private const SERVICE_NOT_RECEIVED = 'SERVICE_NOT_RECEIVED';
    private const SPLIT_SALE = 'SPLIT_SALE';
    private const TRANSFERS_OF_DIVERSE_RESPONSIBILITIES = 'TRANSFERS_OF_DIVERSE_RESPONSIBILITIES';
    private const UNQUALIFIED_CAR_RENTAL_DEBIT = 'UNQUALIFIED_CAR_RENTAL_DEBIT';
    private const USA_CARDHOLDER_DISPUTE = 'USA_CARDHOLDER_DISPUTE';
    private const VISA_FRAUD_MONITORING_PROGRAM = 'VISA_FRAUD_MONITORING_PROGRAM';
    private const WARNING_BULLETIN_FILE = 'WARNING_BULLETIN_FILE';
}
