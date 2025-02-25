<?php

namespace AsaasPaymentGateway\Tests\Unit\Exception;

use AsaasPaymentGateway\Exception\AsaasException;
use PHPUnit\Framework\TestCase;

class AsaasExceptionTest extends TestCase
{
    public function testUndefinedPropertyException(): void
    {
        $field = 'fieldTest';
        $exception = AsaasException::undefinedPropertyException($field);
        $this->assertInstanceOf(AsaasException::class, $exception);
        $this->assertEquals("Undefined property: {$field}", $exception->getMessage());
    }

    public function testRequiredFieldException(): void
    {
        $field = 'fieldTest';
        $exception = AsaasException::requiredFieldException($field);

        $this->assertInstanceOf(AsaasException::class, $exception);
        $this->assertEquals("Field '{$field}' is required.", $exception->getMessage());
    }
}
