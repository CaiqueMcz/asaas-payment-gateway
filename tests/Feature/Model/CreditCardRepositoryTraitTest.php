<?php

namespace CaiqueMcz\AsaasPaymentGateway\Tests\Feature\Model;

use CaiqueMcz\AsaasPaymentGateway\Model\CreditCard;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\CreditCardTrait;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\DataTrait\CreditCardDataTrait;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\GatewayTrait;

class CreditCardRepositoryTraitTest extends BaseRepository
{
    use GatewayTrait;
    use CreditCardDataTrait;
    use CreditCardTrait;

    protected string $defaultCol = "description";
    protected bool $withMock = true;
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
