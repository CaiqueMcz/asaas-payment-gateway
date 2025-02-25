<?php

namespace AsaasPaymentGateway\ValueObject;

interface ArrayableInterface
{
    public static function fromArray(array $data): self;
    public function toArray(): array;
}
