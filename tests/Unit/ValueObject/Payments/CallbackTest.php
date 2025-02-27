<?php

namespace CaiqueMcz\AsaasPaymentGateway\Tests\Unit\ValueObject\Payments;

use CaiqueMcz\AsaasPaymentGateway\Exception\AsaasException;
use CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments\Callback;
use PHPUnit\Framework\TestCase;

class CallbackTest extends TestCase
{
    private string $successUrl;

    protected function setUp(): void
    {
        $this->successUrl = 'https://example.com/success';
    }

    public function testCreateFromConstructor(): void
    {
        $callback = new Callback($this->successUrl);

        $this->assertEquals($this->successUrl, $callback->getSuccessUrl());
        $this->assertTrue($callback->isAutoRedirect());

        // Test with autoRedirect set to false
        $callback = new Callback($this->successUrl, false);
        $this->assertFalse($callback->isAutoRedirect());
    }

    public function testCreateFromArray(): void
    {
        $data = [
            'successUrl' => $this->successUrl,
            'autoRedirect' => false
        ];

        $callback = Callback::fromArray($data);

        $this->assertEquals($this->successUrl, $callback->getSuccessUrl());
        $this->assertFalse($callback->isAutoRedirect());
    }

    public function testCreateFromArrayWithDefaultAutoRedirect(): void
    {
        $data = ['successUrl' => $this->successUrl];
        $callback = Callback::fromArray($data);

        $this->assertEquals($this->successUrl, $callback->getSuccessUrl());
        $this->assertTrue($callback->isAutoRedirect());
    }

    public function testToArray(): void
    {
        $callback = new Callback($this->successUrl, false);
        $array = $callback->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('successUrl', $array);
        $this->assertArrayHasKey('autoRedirect', $array);
        $this->assertEquals($this->successUrl, $array['successUrl']);
        $this->assertFalse($array['autoRedirect']);
    }

    public function testMissingRequiredSuccessUrl(): void
    {
        $this->expectException(AsaasException::class);
        $this->expectExceptionMessage("Field 'successUrl' is required.");

        Callback::fromArray(['autoRedirect' => true]);
    }

    public function testBooleanTypeConversion(): void
    {
        $data = [
            'successUrl' => $this->successUrl,
            'autoRedirect' => 1
        ];

        $callback = Callback::fromArray($data);

        $this->assertIsBool($callback->isAutoRedirect());
        $this->assertTrue($callback->isAutoRedirect());
    }
}
