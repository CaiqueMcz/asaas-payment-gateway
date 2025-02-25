<?php

namespace AsaasPaymentGateway\Enums\Payments;

use MyCLabs\Enum\Enum;

/**
 * DocumentType Enum - Tipo de Documento
 *
 * @method static self INVOICE()
 * @method static self CONTRACT()
 * @method static self MEDIA()
 * @method static self DOCUMENT()
 * @method static self SPREADSHEET()
 * @method static self PROGRAM()
 * @method static self OTHER()
 */
class DocumentType extends Enum
{
    private const INVOICE = 'INVOICE';
    private const CONTRACT = 'CONTRACT';
    private const MEDIA = 'MEDIA';
    private const DOCUMENT = 'DOCUMENT';
    private const SPREADSHEET = 'SPREADSHEET';
    private const PROGRAM = 'PROGRAM';
    private const OTHER = 'OTHER';
}
