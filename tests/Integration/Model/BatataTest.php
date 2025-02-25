<?php

namespace AsaasPaymentGateway\Tests\Integration\Model;

use AsaasPaymentGateway\Exception\AsaasException;
use AsaasPaymentGateway\Model\Installment;
use AsaasPaymentGateway\Tests\Traits\DataTrait\InstallmentDataTrait;
use AsaasPaymentGateway\Tests\Traits\GatewayTrait;
use AsaasPaymentGateway\Tests\Traits\InstallmentTrait;
use AsaasPaymentGateway\Tests\Traits\Model\CreateAbleTrait;
use AsaasPaymentGateway\Tests\Traits\Model\DeleteAbleTrait;
use AsaasPaymentGateway\Tests\Traits\Model\ListAbleTrait;
use AsaasPaymentGateway\Tests\Traits\Model\ShowAbleTrait;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;

class BatataTest extends TestCase implements ModelInterface
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

    /**
     * @throws GuzzleException
     * @throws AsaasException
     */
    public function testCreateWithCreditCard(): Installment
    {
        return $this->createWithCreditCard();
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
