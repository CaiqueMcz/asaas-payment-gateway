<?php

namespace CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments\Limits;

use CaiqueMcz\AsaasPaymentGateway\ValueObject\ArrayableInterface;

class PaymentCreationLimits implements ArrayableInterface
{
    private PaymentDailyLimits $daily;

    public function __construct(PaymentDailyLimits $daily)
    {
        $this->daily = $daily;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            PaymentDailyLimits::fromArray($data['daily'] ?? [])
        );
    }

    public function toArray(): array
    {
        return [
            'daily' => $this->daily->toArray()
        ];
    }

    public function getDaily(): PaymentDailyLimits
    {
        return $this->daily;
    }
}
