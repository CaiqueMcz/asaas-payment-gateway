<?php

namespace CaiqueMcz\AsaasPaymentGateway\Tests\Unit\Model;

use CaiqueMcz\AsaasPaymentGateway\Enums\Splits\SplitStatus;
use CaiqueMcz\AsaasPaymentGateway\Model\Split;
use CaiqueMcz\AsaasPaymentGateway\Repository\SplitRepository;
use CaiqueMcz\AsaasPaymentGateway\Response\ListResponse;
use PHPUnit\Framework\TestCase;

class SplitTest extends TestCase
{
    private array $validData;
    private SplitRepository $mockRepository;

    public function testGetPaid(): void
    {
        $splitId = 'split_123';
        $expectedData = array_merge($this->validData, ['id' => $splitId]);
        $expectedSplit = Split::fromArray($expectedData);

        $this->mockRepository
            ->expects($this->once())
            ->method('getPaid')
            ->with($this->equalTo($splitId))
            ->willReturn($expectedSplit);

        $split = Split::getPaid($splitId);

        $this->assertInstanceOf(Split::class, $split);
        $this->assertEquals($splitId, $split->getId());
    }

    public function testGetReceived(): void
    {
        $splitId = 'split_123';
        $expectedData = array_merge($this->validData, ['id' => $splitId]);
        $expectedSplit = Split::fromArray($expectedData);

        $this->mockRepository
            ->expects($this->once())
            ->method('getReceived')
            ->with($this->equalTo($splitId))
            ->willReturn($expectedSplit);

        $split = Split::getReceived($splitId);

        $this->assertInstanceOf(Split::class, $split);
        $this->assertEquals($splitId, $split->getId());
    }

    public function testGetAllPaid(): void
    {
        $filters = ['status' => SplitStatus::DONE()];
        $listData = [
            'modelClass' => Split::class,
            'object' => 'list',
            'hasMore' => false,
            'totalCount' => 1,
            'limit' => 10,
            'offset' => 0,
            'data' => [$this->validData]
        ];
        $expectedResponse = ListResponse::fromArray($listData);

        $this->mockRepository
            ->expects($this->once())
            ->method('getAllPaid')
            ->with($this->equalTo($filters))
            ->willReturn($expectedResponse);

        $response = Split::getAllPaid($filters);

        $this->assertInstanceOf(ListResponse::class, $response);
        $this->assertEquals(1, $response->getTotalCount());
    }

    public function testGetAllReceived(): void
    {
        $filters = ['status' => SplitStatus::DONE()];
        $listData = [
            'modelClass' => Split::class,
            'object' => 'list',
            'hasMore' => false,
            'totalCount' => 1,
            'limit' => 10,
            'offset' => 0,
            'data' => [$this->validData]
        ];
        $expectedResponse = ListResponse::fromArray($listData);

        $this->mockRepository
            ->expects($this->once())
            ->method('getAllReceived')
            ->with($this->equalTo($filters))
            ->willReturn($expectedResponse);

        $response = Split::getAllReceived($filters);

        $this->assertInstanceOf(ListResponse::class, $response);
        $this->assertEquals(1, $response->getTotalCount());
    }

    protected function setUp(): void
    {
        $this->validData = [
            'walletId' => '7bafd95a-e783-4a62-9be1-23999af742c6',
            'fixedValue' => 20.32,
            'percentualValue' => null,
            'totalValue' => 20.32,
            'status' => SplitStatus::PENDING(),
            'description' => 'Test split'
        ];

        $this->mockRepository = $this->createMock(SplitRepository::class);
        Split::injectRepository(Split::class, $this->mockRepository);
    }

    protected function tearDown(): void
    {
        Split::injectRepository(Split::class, null);
    }
}
