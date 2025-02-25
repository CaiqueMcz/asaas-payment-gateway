<?php

namespace AsaasPaymentGateway\Tests\Unit\ValueObject\Payments;

use AsaasPaymentGateway\ValueObject\Payments\BankSlip;
use PHPUnit\Framework\TestCase;

class BankSlipTest extends TestCase
{
    private BankSlip $bankSlip;

    protected function setUp(): void
    {
        $this->bankSlip = new BankSlip(
            'identification123',
            'nosso123',
            'barcode123',
            'https://example.com/slip',
            5
        );
    }

    public function testCreateFromArray(): void
    {
        $data = [
            'identificationField' => 'identification123',
            'nossoNumero' => 'nosso123',
            'barCode' => 'barcode123',
            'bankSlipUrl' => 'https://example.com/slip',
            'daysAfterDueDateToRegistrationCancellation' => 5
        ];

        $bankSlip = BankSlip::fromArray($data);

        $this->assertEquals('identification123', $bankSlip->getIdentificationField());
        $this->assertEquals('nosso123', $bankSlip->getNossoNumero());
        $this->assertEquals('barcode123', $bankSlip->getBarCode());
        $this->assertEquals('https://example.com/slip', $bankSlip->getBankSlipUrl());
        $this->assertEquals(5, $bankSlip->getDaysAfterDueDateToRegistrationCancellation());
    }

    public function testToArray(): void
    {
        $array = $this->bankSlip->toArray();

        $this->assertIsArray($array);
        $this->assertEquals('identification123', $array['identificationField']);
        $this->assertEquals('nosso123', $array['nossoNumero']);
        $this->assertEquals('barcode123', $array['barCode']);
        $this->assertEquals('https://example.com/slip', $array['bankSlipUrl']);
        $this->assertEquals(5, $array['daysAfterDueDateToRegistrationCancellation']);
    }
}
