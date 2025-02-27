<?php

namespace CaiqueMcz\AsaasPaymentGateway\Tests\Unit\ValueObject\Payments;

use CaiqueMcz\AsaasPaymentGateway\Enums\Payments\DiscountType;
use CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments\Discount;
use PHPUnit\Framework\TestCase;

class DiscountValueObjectTest extends TestCase
{
    public function testCreateFromArray(): void
    {
        $data = [
            'value' => 10.5,
            'dueDateLimitDays' => 5,
            'type' => (string)DiscountType::FIXED()
        ];

        $discount = Discount::fromArray($data);

        $this->assertEquals(10.5, $discount->getValue());
        $this->assertEquals(5, $discount->getDueDateLimitDays());
        $this->assertEquals(DiscountType::FIXED(), $discount->getType());
    }

    public function testToArray(): void
    {
        $discount = new Discount(10.5, 5, DiscountType::PERCENTAGE());
        $array = $discount->toArray();

        $this->assertIsArray($array);
        $this->assertEquals(10.5, $array['value']);
        $this->assertEquals(5, $array['dueDateLimitDays']);
        $this->assertEquals((string)DiscountType::PERCENTAGE(), $array['type']);
    }
}
