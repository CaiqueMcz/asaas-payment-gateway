<?php

namespace AsaasPaymentGateway\ValueObject\Payments;

use AsaasPaymentGateway\ValueObject\ArrayableInterface;

class Installment implements ArrayableInterface
{
    private float $paymentNetValue;
    private float $paymentValue;

    public function __construct(
        float $paymentNetValue,
        float $paymentValue
    ) {
        $this->paymentNetValue = $paymentNetValue;
        $this->paymentValue = $paymentValue;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            (float)$data['paymentNetValue'],
            (float)$data['paymentValue']
        );
    }

    public function toArray(): array
    {
        return [
            'paymentNetValue' => $this->paymentNetValue,
            'paymentValue' => $this->paymentValue
        ];
    }

    public function getPaymentNetValue(): float
    {
        return $this->paymentNetValue;
    }

    public function getPaymentValue(): float
    {
        return $this->paymentValue;
    }
}
