<?php

namespace CaiqueMcz\AsaasPaymentGateway\Tests\Feature\Model;

use CaiqueMcz\AsaasPaymentGateway\Exception\AsaasException;
use CaiqueMcz\AsaasPaymentGateway\Exception\AsaasValidationException;
use CaiqueMcz\AsaasPaymentGateway\Model\AbstractModel;
use CaiqueMcz\AsaasPaymentGateway\Model\Split;
use CaiqueMcz\AsaasPaymentGateway\Model\Subscription;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\DataTrait\SubscriptionDataTrait;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\GatewayTrait;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\Model\CreateAbleTrait;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\Model\DeleteAbleTrait;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\Model\RestoreAbleTrait;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\Model\SearchAbleTrait;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\Model\ShowAbleTrait;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\Model\UpdateAbleTrait;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\SubscriptionTrait;
use GuzzleHttp\Exception\GuzzleException;

class SubscriptionRepositoryTraitTest extends BaseRepository
{
    use SubscriptionDataTrait;
    use GatewayTrait;
    use CreateAbleTrait;
    use SearchAbleTrait;
    use UpdateAbleTrait;
    use DeleteAbleTrait;
    use SubscriptionTrait;
    use ShowAbleTrait;

    protected string $defaultCol = "description";
    protected bool $withMock = true;
    protected $mockRepository = null;

    public function testCreateIntegration(): AbstractModel
    {
        return $this->processCreate();
    }

    /**
     * @depends testCreateIntegration
     */
    public function testShow(AbstractModel $subscription): AbstractModel
    {

        return $this->processShow($subscription);
    }

    /**
     * @depends testCreateIntegration
     */
    public function testSearch(AbstractModel $subscription): AbstractModel
    {

        return $this->processSearch($subscription);
    }

    /**
     * @depends testSearch
     */
    public function testUpdateIntegration(AbstractModel $subscription): AbstractModel
    {
        return $this->processUpdate($subscription);
    }

    /**
     * @depends testUpdateIntegration
     * @throws AsaasException
     * @throws GuzzleException
     * @throws AsaasValidationException
     */
    public function testDelete(AbstractModel $subscription): AbstractModel
    {
        return $this->processDelete($subscription);
    }

    /**
     * @throws AsaasException
     * @throws GuzzleException
     */
    public function testCannotCreateWithInvalidData(): void
    {
        $this->cannotCreateWithCreditCard();
    }

    /**
     * @throws AsaasException
     * @throws GuzzleException
     */
    public function testCreateWithCreditCard(): Subscription
    {
        return $this->createWithCreditCard();
    }

    /**
     * @depends testCreateWithCreditCard
     * @throws AsaasException
     * @throws GuzzleException
     */
    public function testCreateWithCreditCardTokenized(Subscription $subscription): Subscription
    {
        return $this->createWithCreditCardTokenized($subscription);
    }


    protected function setUp(): void
    {
        $this->initGateway();
        $injectedRepo = null;
        if ($this->withMock) {
            $injectedRepo = $this->getMockBuilder($this->getRepositoryClass())
                ->setConstructorArgs([Subscription::class])
                ->getMock();
        }
        $this->mockRepository = $injectedRepo;
        Subscription::injectRepository(Subscription::class, $this->mockRepository);
    }
}
