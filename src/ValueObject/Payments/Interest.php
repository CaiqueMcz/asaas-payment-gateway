<?php

namespace CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments;

use CaiqueMcz\AsaasPaymentGateway\ValueObject\ArrayableInterface;

class Interest implements ArrayableInterface
{
    private float $value;

    public function __construct(float $value)
    {
        $this->value = $value;
    }

    public static function fromArray(array $data): self
    {
        return new self((float)$data['value']);
    }

    public function toArray(): array
    {
        return [
            'value' => $this->value,
        ];
    }

    public function getValue(): float
    {
        return $this->value;
    }
}
