<?php

namespace AsaasPaymentGateway\Tests\Unit\Exception;

use AsaasPaymentGateway\Exception\AsaasValidationException;
use PHPUnit\Framework\TestCase;

class AsaasValidationExceptionTest extends TestCase
{
    public function testExceptionMessageIsSetFromFirstError(): void
    {
        $errors = [
            ['description' => 'First error', 'code' => 'E001'],
            ['description' => 'Second error', 'code' => 'E002'],
        ];

        $exception = new AsaasValidationException($errors);

        // Verify that the exception message is set using the description of the first error.
        $this->assertEquals('First error', $exception->getMessage());
    }

    public function testGetFirstErrorReturnsCorrectError(): void
    {
        $errors = [
            ['description' => 'Initial error', 'code' => 'E100'],
            ['description' => 'Another error', 'code' => 'E200'],
        ];

        $exception = new AsaasValidationException($errors);
        $firstError = $exception->getFirstError();

        // Verify that getFirstError returns the first error from the provided errors array.
        $this->assertEquals($errors[0], $firstError);
    }

    public function testGetErrorsReturnsAllErrors(): void
    {
        $errors = [
            ['description' => 'Error 1', 'code' => 'E001'],
            ['description' => 'Error 2', 'code' => 'E002'],
        ];

        $exception = new AsaasValidationException($errors);

        // Verify that getErrors returns the complete array of errors.
        $this->assertEquals($errors, $exception->getErrors());
    }
}
