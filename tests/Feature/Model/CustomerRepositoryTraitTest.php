<?php

namespace AsaasPaymentGateway\Tests\Feature\Model;

use AsaasPaymentGateway\Exception\AsaasException;
use AsaasPaymentGateway\Exception\AsaasValidationException;
use AsaasPaymentGateway\Model\AbstractModel;
use AsaasPaymentGateway\Model\Customer;
use AsaasPaymentGateway\Tests\Traits\DataTrait\CustomerDataTrait;
use AsaasPaymentGateway\Tests\Traits\GatewayTrait;
use AsaasPaymentGateway\Tests\Traits\Model\CreateAbleTrait;
use AsaasPaymentGateway\Tests\Traits\Model\DeleteAbleTrait;
use AsaasPaymentGateway\Tests\Traits\Model\RestoreAbleTrait;
use AsaasPaymentGateway\Tests\Traits\Model\SearchAbleTrait;
use AsaasPaymentGateway\Tests\Traits\Model\UpdateAbleTrait;
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
