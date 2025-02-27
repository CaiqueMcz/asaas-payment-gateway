<?php

namespace CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments;

use CaiqueMcz\AsaasPaymentGateway\Exception\AsaasException;
use CaiqueMcz\AsaasPaymentGateway\Model\Split;
use CaiqueMcz\AsaasPaymentGateway\ValueObject\ArrayableInterface;

class SplitList implements ArrayableInterface
{
    private $splits;

    /**
     * @throws AsaasException
     */
    public static function fromArray(array $data): self
    {
        $splits = new self();
        foreach ($data as $split) {
            $splits->addSplit(Split::fromArray($split));
        }
        return $splits;
    }

    public function addSplit(Split $split): void
    {
        $this->splits[] = $split;
    }

    public function toArray(): array
    {
        $splits = [];
        if (is_array($this->splits)) {
            foreach ($this->splits as $item) {
                $splits[] = $item->toArray();
            }
        }

        return $splits;
    }

    public function getSplits(): array
    {
        return $this->splits;
    }
}
