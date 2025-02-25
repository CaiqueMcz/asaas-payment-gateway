<?php

namespace AsaasPaymentGateway\Tests\Unit\ValueObject\Payments;

use AsaasPaymentGateway\ValueObject\Payments\PixQrcode;
use PHPUnit\Framework\TestCase;

class PixQrcodeTest extends TestCase
{
    private PixQrcode $pixQrcode;

    protected function setUp(): void
    {
        $this->pixQrcode = new PixQrcode(
            'base64_encoded_image',
            'pix_payload',
            '2025-01-01'
        );
    }

    public function testCreateFromArray(): void
    {
        $data = [
            'encodedImage' => 'base64_encoded_image',
            'payload' => 'pix_payload',
            'expirationDate' => '2025-01-01'
        ];

        $pixQrcode = PixQrcode::fromArray($data);

        $this->assertEquals('base64_encoded_image', $pixQrcode->getEncodedImage());
        $this->assertEquals('pix_payload', $pixQrcode->getPayload());
        $this->assertEquals('2025-01-01', $pixQrcode->getExpirationDate());
    }

    public function testToArray(): void
    {
        $array = $this->pixQrcode->toArray();

        $this->assertIsArray($array);
        $this->assertEquals('base64_encoded_image', $array['encodedImage']);
        $this->assertEquals('pix_payload', $array['payload']);
        $this->assertEquals('2025-01-01', $array['expirationDate']);
    }

    public function testCreateFromEmptyArray(): void
    {
        $pixQrcode = PixQrcode::fromArray([]);

        $this->assertEquals('', $pixQrcode->getEncodedImage());
        $this->assertEquals('', $pixQrcode->getPayload());
        $this->assertEquals('', $pixQrcode->getExpirationDate());
    }
}
