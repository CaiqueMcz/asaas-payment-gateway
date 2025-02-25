<?php

namespace AsaasPaymentGateway\Tests\Integration\Model;

use AsaasPaymentGateway\Exception\AsaasException;
use AsaasPaymentGateway\Model\AbstractModel;
use AsaasPaymentGateway\Model\Payment;
use AsaasPaymentGateway\Tests\Traits\DataTrait\PaymentDataTrait;
use AsaasPaymentGateway\Tests\Traits\GatewayTrait;
use AsaasPaymentGateway\Tests\Traits\Model\CreateAbleTrait;
use AsaasPaymentGateway\Tests\Traits\Model\DeleteAbleTrait;
use AsaasPaymentGateway\Tests\Traits\Model\RestoreAbleTrait;
use AsaasPaymentGateway\Tests\Traits\Model\SearchAbleTrait;
use AsaasPaymentGateway\Tests\Traits\Model\UpdateAbleTrait;
use AsaasPaymentGateway\Tests\Traits\PaymentTrait;
use PHPUnit\Framework\TestCase;

class PaymentTest extends TestCase implements ModelInterface
{
    use GatewayTrait;
    use PaymentDataTrait;
    use CreateAbleTrait;
    use SearchAbleTrait;
    use UpdateAbleTrait;
    use DeleteAbleTrait;
    use RestoreAbleTrait;
    use PaymentTrait;

    protected string $defaultCol = "description";

    protected bool $withMock = false;
    protected $mockRepository = null;

    public function testPaymentWithCreditCard(): AbstractModel
    {
        return $this->paymentWithCreditCard();
    }

    public function testPaymentWithCreditCardTokenized(): AbstractModel
    {
        return $this->payWithCreditCardTokenized();
    }

    /**
     * @depends testPaymentWithCreditCardTokenized
     */
    public function testRefund(Payment $payment): AbstractModel
    {
        return $this->refund($payment);
    }

    public function testCreatePaymentWithCreditCard(): AbstractModel
    {

        return $this->createPaymentWithCreditCard();
    }

    public function testCreatePaymentWithCreditCardTokenized(): AbstractModel
    {

        return $this->createPaymentWithCreditCardTokenized();
    }


    public function testCreatePaymentIntegration(): AbstractModel
    {
        return $this->processCreate();
    }

    /**
     * @depends testCreatePaymentIntegration
     */
    public function testSearchPayment(Payment $payment): AbstractModel
    {

        return $this->processSearch($payment);
    }


    /**
     * @depends testSearchPayment
     */
    public function testUpdatePaymentIntegration(Payment $payment): AbstractModel
    {
        return $this->processUpdate($payment);
    }

    /**
     * @depends testUpdatePaymentIntegration
     */
    public function testDeletePayment(Payment $payment): AbstractModel
    {

        return $this->processDelete($payment);
    }


    /**
     * @depends testDeletePayment
     */
    public function testRestore(Payment $payment): AbstractModel
    {

        return $this->processRestore($payment);
    }

    public function testView(): AbstractModel
    {

        return $this->getViewingInfo();
    }

    /**
     * @depends testView
     */
    public function testIdentificationField(AbstractModel $payment): AbstractModel
    {
        return $this->getIdentificationField($payment);
    }


    /**
     * @depends testView
     */
    public function testGetQrCodePix(Payment $payment): ?AbstractModel
    {
        return $this->getPixQrCode($payment);
    }

    /**
     * @depends testGetQrCodePix
     */
    public function testConfirmReceiveInCash(Payment $payment): ?AbstractModel
    {
        return $this->confirmReceiveInCash($payment);
    }

    /**
     * @depends testConfirmReceiveInCash
     */
    public function testUndoConfirmReceiveInCash(Payment $payment): ?AbstractModel
    {
        return $this->undoConfirmReceiveInCash($payment);
    }

    public function testSimulate(): void
    {
        $this->simulate();
    }

    /**
     * @depends testUndoConfirmReceiveInCash
     * @throws AsaasException
     */
    public function testGetBillingInfo(Payment $payment): Payment
    {
        return $this->getBillingInfo($payment);
    }

    /**
     * @depends testGetBillingInfo
     */
    public function testUploadDocumentSuccess(AbstractModel $payment): ?AbstractModel
    {
        return $this->uploadDocumentSuccess($payment);
    }

    /**
     * @depends testUploadDocumentSuccess
     */
    public function testDocumentList(AbstractModel $payment): ?array
    {
        return $this->getDocumentsList($payment);
    }

    /**
     * @depends testDocumentList
     */
    public function testUpdateDocuments(array $data): ?array
    {
        return $this->updateDocuments($data);
    }

    /**
     * @depends testUpdateDocuments
     */
    public function testGetDocument(array $data): ?array
    {
        return $this->getDocument($data);
    }

    /**
     * @depends testGetDocument
     */
    public function testDeleteDocument(array $data): void
    {
        $this->deleteDocument($data);
    }

    protected function setUp(): void
    {
        $this->initGateway();
        $injectedRepo = null;
        if ($this->withMock) {
            $injectedRepo = $this->createMock($this->getRepositoryClass());
        }
        $this->mockRepository = $injectedRepo;
        Payment::injectRepository(Payment::class, $this->mockRepository);
    }
}
