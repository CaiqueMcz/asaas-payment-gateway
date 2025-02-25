<?php

namespace AsaasPaymentGateway\Tests\Integration\Model;

use AsaasPaymentGateway\Exception\AsaasException;
use AsaasPaymentGateway\Model\AbstractModel;
use AsaasPaymentGateway\Model\Subscription;
use AsaasPaymentGateway\Tests\Traits\DataTrait\SubscriptionDataTrait;
use AsaasPaymentGateway\Tests\Traits\GatewayTrait;
use AsaasPaymentGateway\Tests\Traits\Model\CreateAbleTrait;
use AsaasPaymentGateway\Tests\Traits\Model\DeleteAbleTrait;
use AsaasPaymentGateway\Tests\Traits\Model\RestoreAbleTrait;
use AsaasPaymentGateway\Tests\Traits\Model\SearchAbleTrait;
use AsaasPaymentGateway\Tests\Traits\Model\UpdateAbleTrait;
use AsaasPaymentGateway\Tests\Traits\SubscriptionTrait;
use PHPUnit\Framework\TestCase;

class SubscriptionTest extends TestCase implements ModelInterface
{
    use GatewayTrait;
    use SubscriptionDataTrait;
    use CreateAbleTrait;
    use SearchAbleTrait;
    use UpdateAbleTrait;
    use DeleteAbleTrait;
    use RestoreAbleTrait;
    use SubscriptionTrait;

    protected string $defaultCol = "description";
    protected bool $withMock = false;
    protected $mockRepository = null;

    public function testCreateSubscriptionIntegration(): AbstractModel
    {
        return $this->processCreate();
    }

    /**
     * @depends testCreateSubscriptionIntegration
     */
    public function testSearchSubscription(Subscription $subscription): AbstractModel
    {
        return $this->processSearch($subscription);
    }

    /**
     * @depends testSearchSubscription
     */
    public function testUpdateSubscriptionIntegration(Subscription $subscription): AbstractModel
    {
        return $this->processUpdate($subscription);
    }

    /**
     * @depends testUpdateSubscriptionIntegration
     * @throws AsaasException
     */
    public function testGetPayments(AbstractModel $subscription): AbstractModel
    {
        return $this->processGetPayments($subscription);
    }

    /**
     * @depends testGetPayments
     * @throws AsaasException
     */
    public function testGetPaymentBook(AbstractModel $subscription): AbstractModel
    {
        return $this->processGetPaymentBook($subscription);
    }


    /**
     * @depends testGetPaymentBook
     * @throws AsaasException
     */
    public function testDeleteSubscription(Subscription $subscription): AbstractModel
    {
        return $this->processDelete($subscription);
    }

    /**
     * @depends testDeleteSubscription
     */
    public function testRestoreSubscription(Subscription $subscription): AbstractModel
    {
        return $this->processRestore($subscription);
    }

    protected function setUp(): void
    {
        $this->initGateway();
        $injectedRepo = null;
        if ($this->withMock) {
            $injectedRepo = $this->createMock($this->getRepositoryClass());
        }
        $this->mockRepository = $injectedRepo;
        Subscription::injectRepository(Subscription::class, $this->mockRepository);
    }
}
