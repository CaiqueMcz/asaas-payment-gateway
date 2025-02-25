<?php

namespace AsaasPaymentGateway\Tests\Feature\Model;

use AsaasPaymentGateway\Exception\AsaasException;
use AsaasPaymentGateway\Exception\AsaasValidationException;
use AsaasPaymentGateway\Helpers\Utils;
use AsaasPaymentGateway\Http\Client;
use AsaasPaymentGateway\Model\Payment;
use AsaasPaymentGateway\Tests\Traits\DataTrait\PaymentDataTrait;
use AsaasPaymentGateway\Tests\Traits\GatewayTrait;
use AsaasPaymentGateway\Tests\Traits\Model\CreateAbleTrait;
use AsaasPaymentGateway\Tests\Traits\Model\DeleteAbleTrait;
use AsaasPaymentGateway\Tests\Traits\Model\RestoreAbleTrait;
use AsaasPaymentGateway\Tests\Traits\Model\SearchAbleTrait;
use AsaasPaymentGateway\Tests\Traits\Model\UpdateAbleTrait;
use AsaasPaymentGateway\Tests\Traits\PaymentTrait;
use AsaasPaymentGateway\ValueObject\Payments\PixQrcode;
use Exception;
use GuzzleHttp\Exception\GuzzleException;

class PaymentRepositoryTraitTest extends BaseRepository
{
    use PaymentDataTrait;
    use GatewayTrait;
    use CreateAbleTrait;
    use SearchAbleTrait;
    use UpdateAbleTrait;
    use DeleteAbleTrait;
    use RestoreAbleTrait;
    use PaymentTrait;

    protected string $defaultCol = "description";
    protected bool $withMock = true;
    protected $mockRepository = null;
    protected $clientMock = null;

    public function testCreateIntegration(): Payment
    {
        return $this->processCreate();
    }

    /**
     * @throws AsaasException
     * @throws GuzzleException
     */
    public function testPaymentWithCreditCard(): Payment
    {
        return $this->paymentWithCreditCard();
    }

    /**
     * @throws GuzzleException
     * @throws AsaasException
     */
    public function testPayWithCreditCardTokenized(): Payment
    {
        return $this->payWithCreditCardTokenized();
    }

    /**
     * @throws Exception
     */
    public function testCreatePaymentWithCreditCard(): Payment
    {
        return $this->createPaymentWithCreditCard();
    }

    /**
     * @throws Exception
     */
    public function testCreatePaymentWithCreditCardTokenized(): Payment
    {
        return $this->createPaymentWithCreditCardTokenized();
    }

    /**
     * @depends testPayWithCreditCardTokenized
     */
    public function testRefund(Payment $payment): Payment
    {
        return $this->refund($payment);
    }

    public function testCreatePaymentIntegration(): Payment
    {
        return $this->processCreate();
    }

    /**
     * @depends testCreatePaymentIntegration
     */
    public function testSearchPayment(Payment $Payment): Payment
    {

        return $this->processSearch($Payment);
    }


    /**
     * @depends testSearchPayment
     */
    public function testUpdatePaymentIntegration(Payment $Payment): Payment
    {
        return $this->processUpdate($Payment);
    }

    /**
     * @throws AsaasException
     * @throws GuzzleException
     * @throws AsaasValidationException
     * @depends testUpdatePaymentIntegration
     */
    public function testDeletePayment(Payment $Payment): Payment
    {

        return $this->processDelete($Payment);
    }


    /**
     * @depends testDeletePayment
     */
    public function testRestore(Payment $Payment): Payment
    {
        return $this->processRestore($Payment);
    }

    /**
     * @throws GuzzleException
     * @throws AsaasException
     * @throws Exception
     */
    public function testView(): Payment
    {
        return $this->getViewingInfo();
    }

    /**
     * @depends testView
     * @throws AsaasException
     * @throws GuzzleException
     */
    public function testIdentificationField(Payment $payment): Payment
    {
        return $this->getIdentificationField($payment);
    }

    /**
     * @depends testView
     * @throws AsaasException
     * @throws GuzzleException
     */
    public function testGetQrCodePix(Payment $payment): ?Payment
    {
        return $this->getPixQrCode($payment);
    }

    /**
     * @throws AsaasException
     * @throws GuzzleException
     */
    public function getPixQrCode(Payment $payment): ?Payment
    {
        if ($this->withMock === true) {
            $response = Utils::getPaymentsJsonFile('{id}_pixQrCode.json');
            $this->addInterceptor("get", "payments/{$payment->getId()}/pixQrCode", $response);
        }
        $qrCode = $payment->getPixQrCode();
        $this->assertInstanceOf(PixQrcode::class, $qrCode);
        $this->assertNotNull($qrCode->getEncodedImage());
        $this->assertNotNull($qrCode->getPayload());
        $this->assertNotNull($qrCode->getExpirationDate());
        return $payment;
    }

    /**
     * @depends testGetQrCodePix
     * @throws GuzzleException
     * @throws AsaasException
     */
    public function testConfirmReceiveInCash(Payment $payment): ?Payment
    {
        return $this->confirmReceiveInCash($payment);
    }

    /**
     * @depends testConfirmReceiveInCash
     * @throws GuzzleException
     * @throws AsaasException
     */
    public function testUndoConfirmReceiveInCash(Payment $payment): ?Payment
    {
        return $this->undoConfirmReceiveInCash($payment);
    }


    /**
     * @depends testUndoConfirmReceiveInCash
     * @throws AsaasException
     * @throws GuzzleException
     */
    public function testGetBillingInfo(Payment $payment): Payment
    {
        return $this->getBillingInfo($payment);
    }

    /**
     * @throws Exception
     */
    public function testSimulate(): void
    {
        $this->simulate();
    }

    /**
     * @depends testGetBillingInfo
     * @throws GuzzleException
     */
    public function testUploadDocumentSuccess(Payment $payment): ?Payment
    {
        return $this->uploadDocumentSuccess($payment);
    }

    /**
     * @depends testUploadDocumentSuccess
     * @throws AsaasException
     */
    public function testDocumentList(Payment $payment): ?array
    {
        return $this->getDocumentsList($payment);
    }


    /**
     * @depends testDocumentList
     * @throws Exception
     */
    public function testUpdateDocuments(array $data): ?array
    {
        return $this->updateDocuments($data);
    }

    /**
     * @depends testUpdateDocuments
     * @throws Exception
     */
    public function testGetDocument(array $data): ?array
    {
        return $this->getDocument($data);
    }

    /**
     * @depends testGetDocument
     * @throws Exception
     */
    public function testDeleteDocument(array $data): void
    {
        $this->deleteDocument($data);
    }

    public function testGetLimits(): void
    {
        $this->getLimits();
    }

    /**
     * @throws Exception
     */
    public function testCreatePaymentWithCreditCardPreAuthorized(): Payment
    {
        return $this->createPaymentWithCreditCardPreAuthorized();
    }

    /**
     * @depends testCreatePaymentWithCreditCardPreAuthorized
     * @throws AsaasException
     * @throws GuzzleException
     */
    public function testCaptureAuthorizedPayment(Payment $payment): Payment
    {
        return $this->captureAuthorizedPayment($payment);
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

        $this->clientMock = $this->createMock(Client::class);
    }
}
