<?php

namespace CaiqueMcz\AsaasPaymentGateway\Tests\Integration\Model;

use CaiqueMcz\AsaasPaymentGateway\Exception\AsaasException;
use CaiqueMcz\AsaasPaymentGateway\Model\Installment;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\DataTrait\InstallmentDataTrait;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\GatewayTrait;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\InstallmentTrait;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\Model\CreateAbleTrait;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\Model\DeleteAbleTrait;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\Model\ListAbleTrait;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\Model\ShowAbleTrait;
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
