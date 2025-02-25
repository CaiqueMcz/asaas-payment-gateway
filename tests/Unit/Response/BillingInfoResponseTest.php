<?php

namespace AsaasPaymentGateway\Tests\Unit\Response;

use AsaasPaymentGateway\Response\BillingInfoResponse;
use AsaasPaymentGateway\ValueObject\Payments\BankSlip;
use AsaasPaymentGateway\ValueObject\Payments\CreditCard;
use AsaasPaymentGateway\ValueObject\Payments\PixQrcode;
use PHPUnit\Framework\TestCase;

class BillingInfoResponseTest extends TestCase
{
    private array $pixData;
    private array $creditCardData;
    private array $bankSlipData;

    protected function setUp(): void
    {
        $this->pixData = [
            'encodedImage' => 'base64_encoded_image',
            'payload' => 'pix_payload',
            'expirationDate' => '2024-12-31T23:59:59'
        ];

        $this->creditCardData = [
            'creditCardNumber' => '1234',
            'creditCardBrand' => 'VISA',
            'creditCardToken' => 'token_123'
        ];

        $this->bankSlipData = [
            'identificationField' => '1234567890',
            'nossoNumero' => '987654321',
            'barCode' => '12345678901234567890123456789012345678901234',
            'bankSlipUrl' => 'https://example.com/bankslip',
            'daysAfterDueDateToRegistrationCancellation' => 5
        ];
    }

    public function testFromArrayWithAllData(): void
    {
        $data = [
            'pix' => $this->pixData,
            'creditCard' => $this->creditCardData,
            'bankSlip' => $this->bankSlipData
        ];

        $response = BillingInfoResponse::fromArray($data);

        $this->assertInstanceOf(BillingInfoResponse::class, $response);
        $this->assertInstanceOf(PixQrcode::class, $response->getPix());
        $this->assertInstanceOf(CreditCard::class, $response->getCreditCard());
        $this->assertInstanceOf(BankSlip::class, $response->getBankSlip());
    }

    public function testFromArrayWithOnlyPix(): void
    {
        $data = ['pix' => $this->pixData];
        $response = BillingInfoResponse::fromArray($data);

        $this->assertInstanceOf(PixQrcode::class, $response->getPix());
        $this->assertNull($response->getCreditCard());
        $this->assertNull($response->getBankSlip());

        $pixData = $response->getPix();
        $this->assertEquals($this->pixData['encodedImage'], $pixData->getEncodedImage());
        $this->assertEquals($this->pixData['payload'], $pixData->getPayload());
        $this->assertEquals($this->pixData['expirationDate'], $pixData->getExpirationDate());
    }

    public function testFromArrayWithOnlyCreditCard(): void
    {
        $data = ['creditCard' => $this->creditCardData];
        $response = BillingInfoResponse::fromArray($data);

        $this->assertNull($response->getPix());
        $this->assertInstanceOf(CreditCard::class, $response->getCreditCard());
        $this->assertNull($response->getBankSlip());

        $creditCard = $response->getCreditCard();
        $this->assertEquals($this->creditCardData['creditCardNumber'], $creditCard->getNumber());
        $this->assertEquals($this->creditCardData['creditCardBrand'], $creditCard->getCreditCardBrand());
        $this->assertEquals($this->creditCardData['creditCardToken'], $creditCard->getCreditCardToken());
    }

    public function testFromArrayWithOnlyBankSlip(): void
    {
        $data = ['bankSlip' => $this->bankSlipData];
        $response = BillingInfoResponse::fromArray($data);

        $this->assertNull($response->getPix());
        $this->assertNull($response->getCreditCard());
        $this->assertInstanceOf(BankSlip::class, $response->getBankSlip());

        $bankSlip = $response->getBankSlip();
        $this->assertEquals($this->bankSlipData['identificationField'], $bankSlip->getIdentificationField());
        $this->assertEquals($this->bankSlipData['nossoNumero'], $bankSlip->getNossoNumero());
        $this->assertEquals($this->bankSlipData['barCode'], $bankSlip->getBarCode());
        $this->assertEquals($this->bankSlipData['bankSlipUrl'], $bankSlip->getBankSlipUrl());
        $this->assertEquals(
            $this->bankSlipData['daysAfterDueDateToRegistrationCancellation'],
            $bankSlip->getDaysAfterDueDateToRegistrationCancellation()
        );
    }

    public function testFromArrayWithEmptyData(): void
    {
        $response = BillingInfoResponse::fromArray([]);

        $this->assertNull($response->getPix());
        $this->assertNull($response->getCreditCard());
        $this->assertNull($response->getBankSlip());
    }

    public function testToArrayWithAllData(): void
    {
        $data = [
            'pix' => $this->pixData,
            'creditCard' => $this->creditCardData,
            'bankSlip' => $this->bankSlipData
        ];

        $response = BillingInfoResponse::fromArray($data);
        $array = $response->toArray();

        $this->assertArrayHasKey('pix', $array);
        $this->assertArrayHasKey('creditCard', $array);
        $this->assertArrayHasKey('bankSlip', $array);

        $this->assertEquals($this->pixData, $array['pix']);
        $this->assertEquals($this->creditCardData['creditCardNumber'], $array['creditCard']['number']);
        $this->assertEquals($this->bankSlipData['identificationField'], $array['bankSlip']['identificationField']);
    }

    public function testToArrayWithPartialData(): void
    {
        $data = ['creditCard' => $this->creditCardData];
        $response = BillingInfoResponse::fromArray($data);
        $array = $response->toArray();

        $this->assertArrayHasKey('creditCard', $array);
        $this->assertArrayNotHasKey('pix', $array);
        $this->assertArrayNotHasKey('bankSlip', $array);
    }
}
