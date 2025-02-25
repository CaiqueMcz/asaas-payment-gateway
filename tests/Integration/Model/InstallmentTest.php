<?php

namespace AsaasPaymentGateway\Tests\Integration\Model;

use AsaasPaymentGateway\Model\AbstractModel;
use AsaasPaymentGateway\Model\Installment;
use AsaasPaymentGateway\Tests\Traits\DataTrait\InstallmentDataTrait;
use AsaasPaymentGateway\Tests\Traits\GatewayTrait;
use AsaasPaymentGateway\Tests\Traits\InstallmentTrait;
use AsaasPaymentGateway\Tests\Traits\Model\CreateAbleTrait;
use AsaasPaymentGateway\Tests\Traits\Model\DeleteAbleTrait;
use AsaasPaymentGateway\Tests\Traits\Model\ListAbleTrait;
use AsaasPaymentGateway\Tests\Traits\Model\ShowAbleTrait;
use PHPUnit\Framework\TestCase;

class InstallmentTest extends TestCase implements ModelInterface
{
    use GatewayTrait;
    use InstallmentDataTrait;
    use CreateAbleTrait;
    use DeleteAbleTrait;
    use ListAbleTrait;
    use ShowAbleTrait;
    use InstallmentTrait;

    protected string $defaultCol = "description";
    protected bool $withMock = false;
    protected $mockRepository = null;


    public function testCreateIntegration(): AbstractModel
    {
        return $this->processCreate();
    }

    /**
     * @depends testCreateIntegration
     */
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
