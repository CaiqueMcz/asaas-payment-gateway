<?php

namespace CaiqueMcz\AsaasPaymentGateway\Tests\Feature\Model;

use CaiqueMcz\AsaasPaymentGateway\Exception\AsaasException;
use CaiqueMcz\AsaasPaymentGateway\Exception\AsaasValidationException;
use CaiqueMcz\AsaasPaymentGateway\Model\AbstractModel;
use CaiqueMcz\AsaasPaymentGateway\Model\Installment;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\DataTrait\InstallmentDataTrait;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\GatewayTrait;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\InstallmentTrait;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\Model\CreateAbleTrait;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\Model\DeleteAbleTrait;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\Model\ListAbleTrait;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\Model\ShowAbleTrait;
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
