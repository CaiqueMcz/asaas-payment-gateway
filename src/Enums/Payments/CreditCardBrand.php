<?php

namespace CaiqueMcz\AsaasPaymentGateway\Enums\Payments;

use MyCLabs\Enum\Enum;

/**
 * CreditCardBrand Enum - Marca do Cartão de Crédito
 *
 * @method static self VISA()
 * @method static self MASTERCARD()
 * @method static self ELO()
 * @method static self DINERS()
 * @method static self DISCOVER()
 * @method static self AMEX()
 * @method static self HIPERCARD()
 * @method static self CABAL()
 * @method static self BANESCARD()
 * @method static self CREDZ()
 * @method static self SOROCRED()
 * @method static self CREDSYSTEM()
 * @method static self JCB()
 * @method static self UNKNOWN()
 */
class CreditCardBrand extends Enum
{
    private const VISA = 'VISA';
    private const MASTERCARD = 'MASTERCARD';
    private const ELO = 'ELO';
    private const DINERS = 'DINERS';
    private const DISCOVER = 'DISCOVER';
    private const AMEX = 'AMEX';
    private const HIPERCARD = 'HIPERCARD';
    private const CABAL = 'CABAL';
    private const BANESCARD = 'BANESCARD';
    private const CREDZ = 'CREDZ';
    private const SOROCRED = 'SOROCRED';
    private const CREDSYSTEM = 'CREDSYSTEM';
    private const JCB = 'JCB';
    private const UNKNOWN = 'UNKNOWN';
}
