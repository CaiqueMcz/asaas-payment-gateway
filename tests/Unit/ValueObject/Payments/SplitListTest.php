<?php

namespace AsaasPaymentGateway\Tests\Unit\ValueObject\Payments;

use AsaasPaymentGateway\Exception\AsaasException;
use AsaasPaymentGateway\Model\Split;
use AsaasPaymentGateway\ValueObject\Payments\SplitList;
use PHPUnit\Framework\TestCase;

class SplitListTest extends TestCase
{
    private array $testData;

    public function testFromArrayCreatesSplitListWithValidData(): void
    {
        $splitList = SplitList::fromArray($this->testData);
        $array = $splitList->toArray();

        $this->assertCount(2, $array);
        $this->assertEquals($this->testData[0]['id'], $array[0]['id']);
        $this->assertEquals($this->testData[1]['id'], $array[1]['id']);
    }

    public function testAddSplitAppendsSplitToList(): void
    {
        $splitList = new SplitList();
        $split = Split::fromArray($this->testData[0]);

        $splitList->addSplit($split);
        $array = $splitList->toArray();

        $this->assertCount(1, $array);
        $this->assertEquals($this->testData[0]['id'], $array[0]['id']);
    }

    public function testFromArrayWithEmptyArrayCreatesEmptySplitList(): void
    {
        $splitList = SplitList::fromArray([]);
        $array = $splitList->toArray();
        $this->assertIsArray($array);
        $this->assertEmpty($array);
    }

    public function testMultipleAddSplitsAppendAllSplits(): void
    {
        $splitList = new SplitList();

        foreach ($this->testData as $data) {
            $splitList->addSplit(Split::fromArray($data));
        }

        $array = $splitList->toArray();

        $this->assertCount(2, $array);
        $this->assertEquals($this->testData[0]['walletId'], $array[0]['walletId']);
        $this->assertEquals($this->testData[1]['walletId'], $array[1]['walletId']);
    }

    public function testToArrayPreservesAllSplitData(): void
    {
        $splitList = SplitList::fromArray($this->testData);
        $array = $splitList->toArray();

        foreach ($array as $index => $splitData) {
            $this->assertEquals($this->testData[$index]['id'], $splitData['id']);
            $this->assertEquals($this->testData[$index]['walletId'], $splitData['walletId']);
            $this->assertEquals($this->testData[$index]['fixedValue'], $splitData['fixedValue']);
            $this->assertEquals($this->testData[$index]['status'], $splitData['status']);
        }
    }

    public function testAddSplitAcceptsNullableFields(): void
    {
        $splitData = [
            'walletId' => 'wallet_3',
            'fixedValue' => null,
            'percentualValue' => null,
            'status' => 'PENDING'
        ];

        $splitList = new SplitList();
        $splitList->addSplit(Split::fromArray($splitData));

        $array = $splitList->toArray();
        $firstRow = $array[0];
        $this->assertCount(1, $array);
        $this->assertEquals($splitData['walletId'], $firstRow['walletId']);
        $this->assertArrayNotHasKey("fixedValue", $firstRow);
        $this->assertArrayNotHasKey("percentualValue", $firstRow);
    }

    public function testFromArrayThrowsExceptionWithInvalidData(): void
    {
        $this->expectException(AsaasException::class);

        $invalidData = [
            [
                'id' => 'split_1',
                // walletId missing - should throw exception
                'fixedValue' => 10.50
            ]
        ];

        SplitList::fromArray($invalidData);
    }

    protected function setUp(): void
    {
        $this->testData = [
            [
                'id' => 'split_1',
                'walletId' => 'wallet_1',
                'fixedValue' => 10.50,
                'status' => 'PENDING'
            ],
            [
                'id' => 'split_2',
                'walletId' => 'wallet_2',
                'fixedValue' => 20.75,
                'status' => 'DONE'
            ]
        ];
    }
}
