<?php

namespace AsaasPaymentGateway\Tests\Feature\Model;

use AsaasPaymentGateway\Exception\AsaasException;
use AsaasPaymentGateway\Exception\AsaasValidationException;
use AsaasPaymentGateway\Model\AbstractModel;
use AsaasPaymentGateway\Model\Installment;
use AsaasPaymentGateway\Tests\Traits\DataTrait\InstallmentDataTrait;
use AsaasPaymentGateway\Tests\Traits\GatewayTrait;
use AsaasPaymentGateway\Tests\Traits\InstallmentTrait;
use AsaasPaymentGateway\Tests\Traits\Model\CreateAbleTrait;
use AsaasPaymentGateway\Tests\Traits\Model\DeleteAbleTrait;
use AsaasPaymentGateway\Tests\Traits\Model\ListAbleTrait;
use AsaasPaymentGateway\Tests\Traits\Model\ShowAbleTrait;
use GuzzleHttp\Exception\GuzzleException;

class InstallmentRepositoryTraitTest extends BaseRepository
{
    use GatewayTrait;
    use InstallmentDataTrait;
    use InstallmentTrait;
    use ListAbleTrait;
    use CreateAbleTrait;
    use DeleteAbleTrait;
    use ShowAbleTrait;

    protected string $defaultCol = "paymentExternalReference";
    protected bool $withMock = true;
    protected $mockRepository = null;

    public function testCreateIntegration(): AbstractModel
    {
        return $this->processCreate();
    }


    public function testList(): AbstractModel
    {
        return $this->processList();
    }

    /**
     * @depends testList
     */
    public function testShow(AbstractModel $entity): AbstractModel
    {
        return $this->processShow($entity);
    }

    public function testShowWithInvalidId(): void
    {
        $this->processShowWithInvalidId();
    }

    /**
     * @depends testShow
     */
    public function testGetPayments(AbstractModel $entity): ?AbstractModel
    {
        return $this->processGetPayments($entity);
    }

    /**
     * @depends testShow
     */
    public function testGetPaymentBook(): ?AbstractModel
    {
        return $this->processGetPaymentBook();
    }

    /**
     * @depends testShow
     * @throws AsaasException
     * * @throws GuzzleException
     * * @throws AsaasValidationException
     */
    public function testDelete(AbstractModel $entity): AbstractModel
    {
        return $this->processDelete($entity);
    }

    /**
     * @throws GuzzleException
     * @throws AsaasException
     */
    public function testCreateWithCreditCard(): Installment
    {
        return $this->createWithCreditCard();
    }

    /**
     * @depends testShow
     * @throws GuzzleException
     * @throws AsaasException
     */
    public function testUpdateSplit(Installment $installment): void
    {
        $this->updateSplits($installment);
    }

    /**
     * @depends testCreateWithCreditCard
     * @throws GuzzleException
     * @throws AsaasException
     */
    public function testRefund(Installment $entity): void
    {
        $this->refund($entity);
    }

    protected function setUp(): void
    {
        $this->initGateway();
        $injectedRepo = null;
        if ($this->withMock) {
            $injectedRepo = $this->getMockBuilder($this->getRepositoryClass())
                ->setConstructorArgs([Installment::class])
                ->getMock();
        }
        $this->mockRepository = $injectedRepo;
        Installment::injectRepository(Installment::class, $this->mockRepository);
    }
}
