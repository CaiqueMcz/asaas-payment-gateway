<?php

namespace CaiqueMcz\AsaasPaymentGateway\Tests\Unit\ValueObject;

use CaiqueMcz\AsaasPaymentGateway\ValueObject\SearchParamsBuilder;
use CaiqueMcz\AsaasPaymentGateway\Model\Payment;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class SearchParamsBuilderTest extends TestCase
{
    private SearchParamsBuilder $builder;

    protected function setUp(): void
    {
        $this->builder = new SearchParamsBuilder(Payment::class);
    }

    public function testWhereChaining(): void
    {
        $result = $this->builder
            ->where('customer', 'cus_123')
            ->where('status', 'PENDING');

        $this->assertInstanceOf(SearchParamsBuilder::class, $result);
    }

    public function testBuildQueryParams(): void
    {
        $builder = $this->builder
            ->where('customer', 'cus_123')
            ->where('status', 'PENDING');

        $reflectionClass = new ReflectionClass(SearchParamsBuilder::class);
        $wheresProperty = $reflectionClass->getProperty('wheres');
        $wheresProperty->setAccessible(true);

        $wheres = $wheresProperty->getValue($builder);

        $this->assertIsArray($wheres);
        $this->assertEquals('cus_123', $wheres['customer']);
        $this->assertEquals('PENDING', $wheres['status']);
    }
}
