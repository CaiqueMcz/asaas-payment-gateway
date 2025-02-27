<?php

namespace CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments;

use CaiqueMcz\AsaasPaymentGateway\ValueObject\ArrayableInterface;

class CreditCard implements ArrayableInterface
{
    private ?string $holderName;
    private string $number;
    private ?string $expiryMonth;
    private ?string $expiryYear;
    private ?string $ccv;
    private ?string $creditCardBrand;
    private ?string $creditCardToken;

    public function __construct(
        string $number,
        ?string $holderName = null,
        ?string $expiryMonth = null,
        ?string $expiryYear = null,
        ?string $ccv = null,
        ?string $creditCardBrand = null,
        ?string $creditCardToken = null
    ) {
        $this->number = $number;
        $this->holderName = $holderName;
        $this->expiryMonth = $expiryMonth;
        $this->expiryYear = $expiryYear;
        $this->ccv = $ccv;
        $this->creditCardBrand = $creditCardBrand;
        $this->creditCardToken = $creditCardToken;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['number'] ?? $data['creditCardNumber'] ?? '',
            $data['holderName'] ?? null,
            $data['expiryMonth'] ?? null,
            $data['expiryYear'] ?? null,
            $data['ccv'] ?? null,
            $data['creditCardBrand'] ?? null,
            $data['creditCardToken'] ?? null
        );
    }

    public function toArray(): array
    {

        return [
            'holderName' => $this->holderName,
            'number' => $this->number,
            'expiryMonth' => $this->expiryMonth,
            'expiryYear' => $this->expiryYear,
            'ccv' => $this->ccv,
            'creditCardBrand' => $this->creditCardBrand,
            'creditCardToken' => $this->creditCardToken,
        ];
    }

    public function getHolderName(): ?string
    {
        return $this->holderName;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function getExpiryMonth(): ?string
    {
        return $this->expiryMonth;
    }

    public function getExpiryYear(): ?string
    {
        return $this->expiryYear;
    }

    public function getCcv(): ?string
    {
        return $this->ccv;
    }

    public function getCreditCardBrand(): ?string
    {
        return $this->creditCardBrand;
    }

    public function getCreditCardToken(): ?string
    {
        return $this->creditCardToken;
    }

    public function getLastNumbers(): ?string
    {
        return substr($this->number, -4);
    }
}
