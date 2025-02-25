<?php

namespace AsaasPaymentGateway\ValueObject\Payments\Limits;

use AsaasPaymentGateway\ValueObject\ArrayableInterface;

class PaymentDailyLimits implements ArrayableInterface
{
    private int $used;
    private int $limit;
    private int $remaining;

    public function __construct(
        int $used,
        int $limit,
        int $remaining
    ) {
        $this->used = $used;
        $this->limit = $limit;
        $this->remaining = $remaining;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            (int)($data['used'] ?? 0),
            (int)($data['limit'] ?? 0),
            (int)($data['remaining'] ?? 0)
        );
    }

    public function toArray(): array
    {
        return [
            'used' => $this->used,
            'limit' => $this->limit,
            'remaining' => $this->remaining
        ];
    }

    public function getUsed(): int
    {
        return $this->used;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getRemaining(): int
    {
        return $this->remaining;
    }
}
