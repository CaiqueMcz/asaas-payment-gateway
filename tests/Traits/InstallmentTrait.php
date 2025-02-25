<?php

namespace AsaasPaymentGateway\Tests\Traits;

use AsaasPaymentGateway\Exception\AsaasException;
use AsaasPaymentGateway\Helpers\Utils;
use AsaasPaymentGateway\Model\AbstractModel;
use AsaasPaymentGateway\Model\Installment;
use AsaasPaymentGateway\Model\Payment;
use AsaasPaymentGateway\Model\Split;
use AsaasPaymentGateway\Response\ListResponse;
use AsaasPaymentGateway\ValueObject\Payments\CreditCard;
use AsaasPaymentGateway\ValueObject\Payments\Refund;
use AsaasPaymentGateway\ValueObject\Payments\RefundList;
use AsaasPaymentGateway\ValueObject\Payments\SplitList;
use Exception;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Installment Trait
 * @property \Faker\Generator $faker
 **/
trait InstallmentTrait
{
    /**
     * @throws AsaasException
     * @throws GuzzleException
     */
    public function createWithCreditCard(): Installment
    {
        $randomData = $this->getRandomData();
        $randomData = array_merge($randomData, $this->generateCreditCardData());
        $randomData['remoteIp'] = $this->faker->ipv4;
        /** @var CreditCard $creditCard */
        $creditCard = $randomData['creditCard'];
        if ($this->withMock === true) {
            $response = Utils::getInstallmentsJsonFile(
                "post_create_with_creditcard.json",
                ['lastDigits' => $creditCard->getLastNumbers()]
            );
            $this->addInterceptor('post', 'installments', $response);
        }
        $entity = Installment::create($randomData);
        $this->assertNotNull($entity);
        $this->assertInstanceOf(CreditCard::class, $entity->getCreditCard());
        $this->assertEquals($creditCard->getLastNumbers(), $entity->getCreditCard()->getLastNumbers());
        return $entity;
    }


    /**
     * @throws AsaasException
     * @throws GuzzleException
     */
    public function processGetPayments(AbstractModel $entity): AbstractModel
    {
        if ($this->withMock === true) {
            $expectedResponse = Utils::getInstallmentsJsonFile("get_{id}_payments.json");
            $this->addInterceptor("get", "installments/" . $entity->getId() . "/payments", $expectedResponse);
        }
        $response = $entity->getPayments();
        $this->assertInstanceOf(ListResponse::class, $response);
        $this->assertGreaterThan(0, $response->getTotalCount());
        foreach ($response->getRows() as $row) {
            $this->assertInstanceOf(Payment::class, $row);
        }
        return $entity;
    }

    /**
     * @throws AsaasException
     * @throws GuzzleException
     */
    public function processGetPaymentBook(): Installment
    {
        $entity = $this->getSlipBankInstallment();
        if ($this->withMock === true) {
            $expectedResponse = file_get_contents(Utils::baseDir('tests/Mocks/installments/paymentBook.txt'));
            $this->addInterceptor("get", "installments/" . $entity->getId() . "/paymentBook", [$expectedResponse]);
        }
        $response = $entity->getPaymentBook();
        $this->assertNotEmpty($response);
        return $entity;
    }


    public function getSlipBankInstallment(): Installment
    {
        if ($this->withMock) {
            return Installment::fromArray(Utils::getInstallmentsJsonFile("slipbank.json"));
        }
        return Installment::getById(getenv("ASAAS_SLIPBANK_INSTALLMENT_ID"));
    }

    /**
     * @throws AsaasException
     * @throws GuzzleException
     * @throws Exception
     */
    public function refund(Installment $entity): void
    {
        $refundId = $entity->getId();
        if ($this->withMock === true) {
            $response = Utils::getInstallmentsJsonFile(
                "post_{id}_refund.json",
                ['refundId' => $refundId]
            );
            $this->addInterceptor('post', "installments/$refundId/refund", $response);
        }
        $refund = $entity->refund();
        $this->assertInstanceOf(Installment::class, $refund);
        $this->assertInstanceOf(RefundList::class, $refund->getRefundsList());
        foreach ($refund->getRefundsList()->getRefunds() as $refund) {
            $this->assertInstanceOf(Refund::class, $refund);
        }
    }

    /**
     * @throws AsaasException
     */
    public function updateSplits(Installment $entity): void
    {
        $splitList = new SplitList();
        $splitList->addSplit($this->getRandSplit());
        if ($this->withMock === true) {
            $expectedResponse = Utils::getInstallmentsJsonFile("put_{id}_splits.json");
            $this->addInterceptor("put", "installments/" . $entity->getId() . "/splits", $expectedResponse);
        }
        $response = $entity->updateSplits($splitList);
        $this->assertNotEmpty($response);
        $this->assertInstanceOf(SplitList::class, $response);
        foreach ($response->getSplits() as $split) {
            $this->assertInstanceOf(Split::class, $split);
        }
    }
}
