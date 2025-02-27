<?php

namespace CaiqueMcz\AsaasPaymentGateway\Tests\Feature\Model;

use CaiqueMcz\AsaasPaymentGateway\Exception\AsaasException;
use CaiqueMcz\AsaasPaymentGateway\Exception\AsaasValidationException;
use CaiqueMcz\AsaasPaymentGateway\Model\AbstractModel;
use CaiqueMcz\AsaasPaymentGateway\Model\Customer;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\DataTrait\CustomerDataTrait;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\GatewayTrait;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\Model\CreateAbleTrait;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\Model\DeleteAbleTrait;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\Model\RestoreAbleTrait;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\Model\SearchAbleTrait;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\Model\UpdateAbleTrait;
use GuzzleHttp\Exception\GuzzleException;

class CustomerRepositoryTraitTest extends BaseRepository
{
    use CustomerDataTrait;
    use GatewayTrait;
    use CreateAbleTrait;
    use SearchAbleTrait;
    use UpdateAbleTrait;
    use DeleteAbleTrait;
    use RestoreAbleTrait;

    protected string $defaultCol = "email";
    protected bool $withMock = true;
    protected $mockRepository = null;

    public function testCreateCustomerIntegration(): AbstractModel
    {
        return $this->processCreate();
    }

    /**
     * @depends testCreateCustomerIntegration
     */
    public function testSearchCustomer(Customer $customer): AbstractModel
    {

        return $this->processSearch($customer);
    }


    /**
     * @depends testSearchCustomer
     */
    public function testUpdateCustomerIntegration(Customer $customer): AbstractModel
    {
        return $this->processUpdate($customer);
    }

    /**
     * @depends testUpdateCustomerIntegration
     * @throws AsaasException
     * @throws GuzzleException
     * @throws AsaasValidationException
     */
    public function testDeleteCustomer(Customer $customer): AbstractModel
    {

        return $this->processDelete($customer);
    }


    /**
     * @depends testDeleteCustomer
     */
    public function testRestore(Customer $customer): AbstractModel
    {

        return $this->processRestore($customer);
    }

    protected function setUp(): void
    {
        $this->initGateway();
        $injectedRepo = null;
        if ($this->withMock) {
            $injectedRepo = $this->createMock($this->getRepositoryClass());
        }
        $this->mockRepository = $injectedRepo;
        Customer::injectRepository(Customer::class, $this->mockRepository);
    }
}
