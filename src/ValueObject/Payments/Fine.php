<?php

namespace CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments;

use CaiqueMcz\AsaasPaymentGateway\Enums\Payments\FineType;
use CaiqueMcz\AsaasPaymentGateway\ValueObject\ArrayableInterface;

class Fine implements ArrayableInterface
{
    private float $value;
    private ?FineType $type;

    public function __construct(float $value, ?FineType $type)
    {
        $this->value = $value;
        $this->type = $type;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            (float)$data['value'],
            isset($data['type']) ? FineType::from($data['type']) : null
        );
    }

    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'type' => $this->type,
        ];
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function getType(): ?FineType
    {
        return $this->type;
    }
}
