<?php

namespace CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments\Limits;

use CaiqueMcz\AsaasPaymentGateway\ValueObject\ArrayableInterface;

class PaymentLimitsResponse implements ArrayableInterface
{
    private string $object;
    private PaymentCreationLimits $creation;

    public function __construct(
        string $object,
        PaymentCreationLimits $creation
    ) {
        $this->object = $object;
        $this->creation = $creation;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['object'] ?? 'limits',
            PaymentCreationLimits::fromArray($data['creation'] ?? [])
        );
    }

    public function toArray(): array
    {
        return [
            'object' => $this->object,
            'creation' => $this->creation->toArray()
        ];
    }

    public function getObject(): string
    {
        return $this->object;
    }

    public function getCreation(): PaymentCreationLimits
    {
        return $this->creation;
    }
}
