<?php

namespace CaiqueMcz\AsaasPaymentGateway\Tests\Integration\Model;

use CaiqueMcz\AsaasPaymentGateway\Exception\AsaasException;
use CaiqueMcz\AsaasPaymentGateway\Model\AbstractModel;
use CaiqueMcz\AsaasPaymentGateway\Model\Subscription;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\DataTrait\SubscriptionDataTrait;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\GatewayTrait;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\Model\CreateAbleTrait;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\Model\DeleteAbleTrait;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\Model\RestoreAbleTrait;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\Model\SearchAbleTrait;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\Model\UpdateAbleTrait;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\SubscriptionTrait;
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
