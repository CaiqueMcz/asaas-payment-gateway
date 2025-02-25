<?php

namespace AsaasPaymentGateway\Tests\Unit\ValueObject\Payments;

use AsaasPaymentGateway\Enums\Payments\FineType;
use AsaasPaymentGateway\ValueObject\Payments\Fine;
use PHPUnit\Framework\TestCase;

class FineTest extends TestCase
{
    public function testCreateFromArray(): void
    {
        $data = [
            'value' => 10.5,
            'type' => (string)FineType::FIXED()
        ];

        $fine = Fine::fromArray($data);

        $this->assertEquals(10.5, $fine->getValue());
        $this->assertEquals(FineType::FIXED(), $fine->getType());
    }

    public function testToArray(): void
    {
        $fine = new Fine(10.5, FineType::PERCENTAGE());
        $array = $fine->toArray();

        $this->assertIsArray($array);
        $this->assertEquals(10.5, $array['value']);
        $this->assertEquals((string)FineType::PERCENTAGE(), $array['type']);
    }
}
