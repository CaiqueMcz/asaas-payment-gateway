<?php

namespace CaiqueMcz\AsaasPaymentGateway\Tests\Unit\Model;

use PHPUnit\Framework\TestCase;
use CaiqueMcz\AsaasPaymentGateway\Model\Customer;
use CaiqueMcz\AsaasPaymentGateway\Exception\AsaasException;

class CustomerTest extends TestCase
{
    /**
     * @throws AsaasException
     */
    public function testCustomerCreationAndAccessors(): void
    {
        $data = [
            'id'                   => 123,
            'name'                 => 'John Doe',
            'cpfCnpj'              => '12345678901',
            'email'                => 'john@example.com',
            'notificationDisabled' => true,
            'foreignCustomer'      => false,
        ];
        $customer = new Customer($data);
        $this->assertEquals(123, $customer->getId());
        $this->assertEquals('John Doe', $customer->getName());
        $this->assertEquals('12345678901', $customer->getCpfCnpj());
        $this->assertEquals('john@example.com', $customer->getEmail());
        $this->assertTrue($customer->isNotificationDisabled());
        $this->assertFalse($customer->isForeignCustomer());
        $array = $customer->toArray();
        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('cpfCnpj', $array);
    }

    public function testUndefinedPropertyThrowsException(): void
    {
        $this->expectException(AsaasException::class);
        $this->expectExceptionMessage("Undefined property: unknown");
        $data = [
            'name'    => 'John Doe',
            'cpfCnpj' => '12345678901'
        ];
        $customer = new Customer($data);
        $customer->unknown;
    }

    /**
     * @throws AsaasException
     */
    public function testMagicCallSettersAndGetters(): void
    {
        $data = [
            'name'    => 'John Doe',
            'cpfCnpj' => '12345678901'
        ];
        $customer = new Customer($data);
        $customer->setEmail('john@example.com');
        $this->assertEquals('john@example.com', $customer->getEmail());
    }
}
