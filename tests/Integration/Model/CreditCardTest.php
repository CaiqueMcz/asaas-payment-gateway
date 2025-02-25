<?php

namespace AsaasPaymentGateway\Tests\Integration\Model;

use AsaasPaymentGateway\Model\CreditCard;
use AsaasPaymentGateway\Tests\Traits\CreditCardTrait;
use AsaasPaymentGateway\Tests\Traits\DataTrait\CreditCardDataTrait;
use AsaasPaymentGateway\Tests\Traits\GatewayTrait;
use PHPUnit\Framework\TestCase;

class CreditCardTest extends TestCase implements ModelInterface
{
    use GatewayTrait;
    use CreditCardDataTrait;
    use CreditCardTrait;

    protected string $defaultCol = "description";

    protected bool $withMock = false;
    protected $mockRepository = null;

    public function testCanTokenizeCreditCard()
    {

        return $this->tokenizeCreditCard();
    }

    protected function setUp(): void
    {
        $this->initGateway();
        $injectedRepo = null;
        if ($this->withMock) {
            $injectedRepo = $this->createMock($this->getRepositoryClass());
        }
        $this->mockRepository = $injectedRepo;
        CreditCard::injectRepository(CreditCard::class, $this->mockRepository);
    }
}
