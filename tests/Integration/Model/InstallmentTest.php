<?php

namespace CaiqueMcz\AsaasPaymentGateway\Tests\Integration\Model;

use CaiqueMcz\AsaasPaymentGateway\Model\AbstractModel;
use CaiqueMcz\AsaasPaymentGateway\Model\Installment;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\DataTrait\InstallmentDataTrait;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\GatewayTrait;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\InstallmentTrait;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\Model\CreateAbleTrait;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\Model\DeleteAbleTrait;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\Model\ListAbleTrait;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\Model\ShowAbleTrait;
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
