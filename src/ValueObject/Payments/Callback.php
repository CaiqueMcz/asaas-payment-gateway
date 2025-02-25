<?php

namespace AsaasPaymentGateway\ValueObject\Payments;

use AsaasPaymentGateway\Exception\AsaasException;
use AsaasPaymentGateway\ValueObject\ArrayableInterface;

class Callback implements ArrayableInterface
{
    private string $successUrl;
    private bool $autoRedirect;

    public function __construct(
        string $successUrl,
        bool $autoRedirect = true
    ) {
        $this->successUrl = $successUrl;
        $this->autoRedirect = $autoRedirect;
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['successUrl'])) {
            throw new AsaasException("Field 'successUrl' is required.");
        }

        return new self(
            $data['successUrl'],
            isset($data['autoRedirect']) ? (bool)$data['autoRedirect'] : true
        );
    }

    public function toArray(): array
    {
        return [
            'successUrl' => $this->successUrl,
            'autoRedirect' => $this->autoRedirect
        ];
    }

    public function getSuccessUrl(): string
    {
        return $this->successUrl;
    }

    public function isAutoRedirect(): bool
    {
        return $this->autoRedirect;
    }
}
