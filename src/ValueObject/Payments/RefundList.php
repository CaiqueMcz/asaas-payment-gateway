<?php

namespace CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments;

use CaiqueMcz\AsaasPaymentGateway\Exception\AsaasException;
use CaiqueMcz\AsaasPaymentGateway\ValueObject\ArrayableInterface;

class RefundList implements ArrayableInterface
{
    private array $refunds = [];

    public static function fromArray(array $data): self
    {
        $refundList = new self();
        foreach ($data as $refund) {
            $refundList->addRefund(Refund::fromArray($refund));
        }
        return $refundList;
    }

    public function addRefund(Refund $refund): void
    {
        $this->refunds[] = $refund;
    }

    public function toArray(): array
    {
        $refunds = [];
        foreach ($this->refunds as $refund) {
            $refunds[] = $refund->toArray();
        }
        return $refunds;
    }

    /**
     * @return Refund[]
     */
    public function getRefunds(): array
    {
        return $this->refunds;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->refunds);
    }

    /**
     * @param int $index
     * @return Refund|null
     */
    public function getRefundAt(int $index): ?Refund
    {
        return $this->refunds[$index] ?? null;
    }
}
