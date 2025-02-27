<?php

namespace CaiqueMcz\AsaasPaymentGateway\Tests\Traits;

use CaiqueMcz\AsaasPaymentGateway\Exception\AsaasException;
use CaiqueMcz\AsaasPaymentGateway\Helpers\Utils;
use CaiqueMcz\AsaasPaymentGateway\Model\Split;
use CaiqueMcz\AsaasPaymentGateway\Repository\SplitRepository;
use CaiqueMcz\AsaasPaymentGateway\Response\ListResponse;

trait SplitTrait
{
    /**
     * @throws AsaasException
     * @throws \Exception
     */
    public function processGetAllPaid(): ListResponse
    {
        if ($this->withMock === true) {
            $response = Utils::getSplitsJsonFile("list_paid.json");
            $this->addInterceptor('get', "payments/splits/paid", $response);
        }
        $splits = Split::getAllPaid();
        $this->assertInstanceof(ListResponse::class, $splits);
        if ($splits->getTotalCount() > 0) {
            foreach ($splits->getRows() as $split) {
                $this->assertInstanceof(Split::class, $split);
            }
        }
        return $splits;
    }

    /**
     * @throws AsaasException
     * @throws \Exception
     */
    public function processGetPaid(ListResponse $listResponse): void
    {

        $this->assertGreaterThanOrEqual(0, $listResponse->getTotalCount());
        if ($listResponse->getTotalCount() > 0) {
            $firstRow = $listResponse->getFirstRow();
            $this->assertInstanceof(Split::class, $firstRow);
            $this->assertNotNull($firstRow->getId());
            if ($this->withMock === true) {
                $response = Utils::getSplitsJsonFile("paid.json");
                $this->addInterceptor('get', "payments/splits/paid/{$firstRow->getId()}", $response);
            }
            $row = Split::getPaid($firstRow->getId());
            $this->assertInstanceof(Split::class, $row);
            $this->assertNotNull($row->getId());
            $this->assertEquals($row->getId(), $firstRow->getId());
        }
    }

    /**
     * @throws AsaasException
     * @throws \Exception
     */
    public function processGetAllReceived(): ListResponse
    {
        if ($this->withMock === true) {
            $response = Utils::getSplitsJsonFile("list_received.json");
            $this->addInterceptor('get', "payments/splits/received", $response);
        }
        $splits = Split::getAllReceived();
        $this->assertInstanceof(ListResponse::class, $splits);
        if ($splits->getTotalCount() > 0) {
            foreach ($splits->getRows() as $split) {
                $this->assertInstanceof(Split::class, $split);
            }
        }
        return $splits;
    }

    /**
     * @throws AsaasException
     * @throws \Exception
     */
    public function processGetReceived(ListResponse $listResponse): void
    {
        $this->assertGreaterThanOrEqual(0, $listResponse->getTotalCount());
        if ($listResponse->getTotalCount() > 0) {
            $firstRow = $listResponse->getFirstRow();
            $this->assertInstanceof(Split::class, $firstRow);
            $this->assertNotNull($firstRow->getId());
            if ($this->withMock === true) {
                $response = Utils::getSplitsJsonFile("received.json");
                $this->addInterceptor('get', "payments/splits/received/{$firstRow->getId()}", $response);
            }
            $row = Split::getReceived($firstRow->getId());
            $this->assertInstanceof(Split::class, $row);
            $this->assertNotNull($row->getId());
            $this->assertEquals($row->getId(), $firstRow->getId());
        }
    }

    public function getFieldInfos(): array
    {
        return [];
    }

    public function getModelClass(): string
    {
        return Split::class;
    }

    public function getRepositoryClass(): string
    {
        return SplitRepository::class;
    }
}
