<?php

namespace CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments;

use CaiqueMcz\AsaasPaymentGateway\ValueObject\ArrayableInterface;

class PixQrcode implements ArrayableInterface
{
    private string $encodedImage;
    private string $payload;
    private string $expirationDate;

    public function __construct(string $encodedImage, string $payload, string $expirationDate)
    {
        $this->encodedImage = $encodedImage;
        $this->payload = $payload;
        $this->expirationDate = $expirationDate;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['encodedImage'] ?? '',
            $data['payload'] ?? '',
            $data['expirationDate'] ?? ''
        );
    }

    public function toArray(): array
    {
        return [
            'encodedImage'   => $this->encodedImage,
            'payload'        => $this->payload,
            'expirationDate' => $this->expirationDate,
        ];
    }

    public function getEncodedImage(): string
    {
        return $this->encodedImage;
    }

    public function getPayload(): string
    {
        return $this->payload;
    }

    public function getExpirationDate(): string
    {
        return $this->expirationDate;
    }
}
