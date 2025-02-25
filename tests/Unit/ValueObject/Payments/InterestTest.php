<?php

namespace AsaasPaymentGateway\Tests\Unit\ValueObject\Payments;

use AsaasPaymentGateway\ValueObject\Payments\Interest;
use PHPUnit\Framework\TestCase;

class InterestTest extends TestCase
{
    public function testCreateFromArray(): void
    {
        $data = ['value' => 10.5];
        $interest = Interest::fromArray($data);

        $this->assertEquals(10.5, $interest->getValue());
    }

    public function testToArray(): void
    {
        $interest = new Interest(10.5);
        $array = $interest->toArray();

        $this->assertIsArray($array);
        $this->assertEquals(10.5, $array['value']);
    }
}
