<?php

namespace CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments;

use CaiqueMcz\AsaasPaymentGateway\Enums\Payments\DiscountType;
use CaiqueMcz\AsaasPaymentGateway\ValueObject\ArrayableInterface;

class Discount implements ArrayableInterface
{
    private float $value;
    private int $dueDateLimitDays;
    private ?DiscountType $type;

    public function __construct(float $value, int $dueDateLimitDays, ?DiscountType $type)
    {
        $this->value = $value;
        $this->dueDateLimitDays = $dueDateLimitDays;
        $this->type = $type;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            (float)$data['value'],
            (int)$data['dueDateLimitDays'],
            isset($data['type']) ? DiscountType::from($data['type']) : null,
        );
    }

    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'dueDateLimitDays' => $this->dueDateLimitDays,
            'type' => (string)$this->type,
        ];
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function getDueDateLimitDays(): int
    {
        return $this->dueDateLimitDays;
    }

    public function getType(): ?DiscountType
    {
        return $this->type;
    }
}
