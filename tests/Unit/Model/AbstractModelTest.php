<?php

namespace CaiqueMcz\AsaasPaymentGateway\Tests\Unit\Model;

use CaiqueMcz\AsaasPaymentGateway\Tests\Unit\Model\AbstractModel\TestModel;
use PHPUnit\Framework\TestCase;
use CaiqueMcz\AsaasPaymentGateway\Exception\AsaasException;

class AbstractModelTest extends TestCase
{
    /**
     * @throws AsaasException
     */
    public function testConstructorSetsAttributesAndCastsValues(): void
    {
        $data = [
            'id'     => '10',
            'name'   => 'Test',
            'age'    => '25',
            'active' => '1',
        ];
        $model = new TestModel($data);
        $this->assertEquals(10, $model->getId());
        $this->assertEquals('Test', $model->getName());
        $this->assertEquals(25, $model->getAge());
        $this->assertTrue($model->isActive());
    }

    public function testMissingRequiredFieldThrowsException(): void
    {
        $this->expectException(AsaasException::class);
        $this->expectExceptionMessage("Field 'name' is required.");
        $data = [
            'id'     => '10',
            'age'    => '25',
            'active' => '1',
        ];
        new TestModel($data);
    }

    /**
     * @throws AsaasException
     */
    public function testToArrayReturnsAllFields(): void
    {
        $data = [
            'id'     => '10',
            'name'   => 'Test',
            'age'    => '25',
            'active' => '1',
        ];
        $model = new TestModel($data);
        $array = $model->toArray();
        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('age', $array);
        $this->assertArrayHasKey('active', $array);
        $this->assertEquals(10, $array['id']);
        $this->assertEquals('Test', $array['name']);
        $this->assertEquals(25, $array['age']);
        $this->assertTrue($array['active']);
    }

    public function testMagicGetThrowsExceptionForUndefinedProperty(): void
    {
        $this->expectException(AsaasException::class);
        $this->expectExceptionMessage("Undefined property: unknown");
        $data = ['name' => 'Test'];
        $model = new TestModel($data);
        $model->unknown;
    }

    public function testMagicSetThrowsExceptionForUndefinedProperty(): void
    {
        $this->expectException(AsaasException::class);
        $this->expectExceptionMessage("Undefined property: unknown");
        $data = ['name' => 'Test'];
        $model = new TestModel($data);
        $model->unknown = 'value';
    }

    /**
     * @throws AsaasException
     */
    public function testMagicCallGetterAndSetter(): void
    {
        $data = [
            'id'     => '10',
            'name'   => 'Test',
            'age'    => '25',
            'active' => '1',
        ];
        $model = new TestModel($data);
        $this->assertEquals(10, $model->getId());
        $this->assertEquals('Test', $model->getName());
        $this->assertEquals(25, $model->getAge());
        $this->assertTrue($model->isActive());
        $model->setName('New Name');
        $this->assertEquals('New Name', $model->getName());
    }
}
