<?php

namespace CaiqueMcz\AsaasPaymentGateway\Tests\Traits;

use CaiqueMcz\AsaasPaymentGateway\Enums\Payments\BillingType;
use CaiqueMcz\AsaasPaymentGateway\Enums\Payments\DocumentType;
use CaiqueMcz\AsaasPaymentGateway\Enums\Payments\PaymentStatus;
use CaiqueMcz\AsaasPaymentGateway\Exception\AsaasException;
use CaiqueMcz\AsaasPaymentGateway\Helpers\Utils;
use CaiqueMcz\AsaasPaymentGateway\Model\Payment;
use CaiqueMcz\AsaasPaymentGateway\Response\BillingInfoResponse;
use CaiqueMcz\AsaasPaymentGateway\Response\ListResponse;
use CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments\CreditCard;
use CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments\CreditCardHolderInfo;
use CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments\Installment;
use CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments\Limits\PaymentLimitsResponse;
use CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments\PaymentDocument;
use CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments\PaymentDocumentFile;
use CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments\PixQrcode;
use Exception;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Payment Model
 * @method Payment|null processCreate()
 * @method Payment|null payWithCreditCard(CreditCard $creditCard, CreditCardHolderInfo $creditCardHolderInfo)
 * @method Payment      processRestore(Payment $entity)
 * @method Payment processDelete(Payment $entity)
 * @method Payment processSearch(Payment $entity)
 **/
trait PaymentTrait
{
    /**
     * @throws Exception
     */
    public function createPaymentWithCreditCard(): Payment
    {
        $replaceData = $this->getRandomData();
        $replaceData['billingType'] = BillingType::CREDIT_CARD();
        $replaceData['remoteIp'] = $this->faker->ipv4;
        unset($replaceData['discount']);
        $replaceData = array_merge($replaceData, $this->generateCreditCardData());
        $createResponse = $this->processCreate($replaceData, "createWithCreditCard");
        $this->assertEquals(BillingType::CREDIT_CARD(), $createResponse->getBillingType());
        return $createResponse;
    }


    /**
     * @throws Exception
     */
    public function createPaymentWithCreditCardTokenized(): Payment
    {

        $replaceData = $this->getRandomData();
        $replaceData['billingType'] = BillingType::CREDIT_CARD();
        $replaceData['remoteIp'] = $this->faker->ipv4;
        unset($replaceData['discount']);
        $replaceData = array_merge($replaceData, ['creditCardToken' => getenv("ASAAS_CREDIT_CARD_TOKENIZED")]);
        $createResponse = $this->processCreate($replaceData, "createWithCreditCardTokenized");
        $this->assertEquals(BillingType::CREDIT_CARD(), $createResponse->getBillingType());
        return $createResponse;
    }

    /**
     * @throws GuzzleException
     * @throws AsaasException
     * @throws Exception
     */
    public function paymentWithCreditCard(): Payment
    {
        $replaceData = $this->getRandomData();
        $replaceData['billingType'] = BillingType::CREDIT_CARD();
        unset($replaceData['discount']);
        /**
         * @var Payment $createResponse
         */
        $createResponse = $this->processCreate($replaceData, "create");
        $this->assertNotNull($createResponse->getId());
        $creditCardData = $this->generateCreditCardData();
        if ($this->withMock === true) {
            $entityClass = $this->getModelClass();
            $data = $createResponse->toArray();
            $data['status'] = PaymentStatus::CONFIRMED();
            $expectedEntity = call_user_func([$entityClass, 'fromArray'], $data);
            $this->mockRepository
                ->expects($this->once())
                ->method('payWithCreditCard')
                ->with($this->equalTo($expectedEntity->getId()))
                ->willReturn($expectedEntity);
        } else {
            $this->assertEquals(PaymentStatus::PENDING(), $createResponse->getStatus());
        }

        $response = $createResponse->payWithCreditCard(
            $creditCardData['creditCard'],
            $creditCardData['creditCardHolderInfo']
        );
        $this->assertEquals(PaymentStatus::CONFIRMED(), $response->getStatus());
        return $response;
    }


    /**
     * @throws GuzzleException
     * @throws AsaasException
     * @throws Exception
     */
    public function payWithCreditCardTokenized(): ?Payment
    {
        $replaceData = $this->getRandomData();
        $replaceData['billingType'] = BillingType::CREDIT_CARD();
        unset($replaceData['discount']);
        $createResponse = $this->processCreate($replaceData, "create");
        $this->assertNotNull($createResponse->getId());
        $creditCardToken = getenv("ASAAS_CREDIT_CARD_TOKENIZED");
        if ($this->withMock === true) {
            $entityClass = $this->getModelClass();
            $data = $createResponse->toArray();
            $data['status'] = PaymentStatus::CONFIRMED();
            $expectedEntity = call_user_func([$entityClass, 'fromArray'], $data);
            $this->mockRepository
                ->expects($this->once())
                ->method('payWithCreditCard')
                ->with($this->equalTo($expectedEntity->getId()))
                ->willReturn($expectedEntity);
        } else {
            $this->assertEquals(PaymentStatus::PENDING(), $createResponse->getStatus());
        }
        $response = $createResponse->payWithCreditCardTokenized($creditCardToken);
        $this->assertEquals(PaymentStatus::CONFIRMED(), $response->getStatus());
        return $response;
    }

    /**
     * @throws GuzzleException
     * @throws AsaasException
     */
    public function refund(Payment $payment): ?Payment
    {
        $this->assertEquals(PaymentStatus::CONFIRMED(), $payment->getStatus());
        if ($this->withMock === true) {
            $paymentAsArray = $payment->toArray();
            $entityClass = $this->getModelClass();
            $paymentAsArray['status'] = PaymentStatus::REFUNDED();
            $expectedEntity = call_user_func([$entityClass, 'fromArray'], $paymentAsArray);
            $this->mockRepository
                ->expects($this->once())
                ->method('refund')
                ->with($this->equalTo($payment->getId()))
                ->willReturn($expectedEntity);
        }
        $payment = $payment->refund();
        $this->assertEquals(PaymentStatus::REFUNDED(), $payment->getStatus());
        return $payment;
    }

    /**
     * @throws GuzzleException
     * @throws AsaasException
     * @throws Exception
     */
    public function getViewingInfo(): ?Payment
    {

        $replaceData = $this->getRandomData();
        $replaceData['billingType'] = BillingType::BOLETO();
        unset($replaceData['discount']);
        $payment = $this->processCreate($replaceData, "create");
        $paymentId = $payment->getId();
        $this->assertNotNull($payment->getId());
        if ($this->withMock === true) {
            $expectedResponse = [
                "invoiceViewedDate" => null, "boletoViewedDate" => null
            ];
            $this->addInterceptor('get', "payments/$paymentId/viewingInfo", $expectedResponse);
        }
        $response = $payment->getViewingInfo();
        $this->assertArrayHasKey('invoiceViewedDate', $response);
        $this->assertArrayHasKey('boletoViewedDate', $response);

        return $payment;
    }

    /**
     * @throws GuzzleException
     * @throws AsaasException
     */
    public function confirmReceiveInCash(Payment $payment): ?Payment
    {
        $paymentId = $payment->getId();
        $paymentAsArray = $payment->toArray();
        if ($this->withMock === true) {
            $paymentAsArray['status'] = PaymentStatus::RECEIVED_IN_CASH();
            $this->addInterceptor("post", "payments/$paymentId/receiveInCash", $paymentAsArray);
        }
        $payment = $payment->confirmReceiveInCash(date("Y-m-d"), $payment->getValue(), false);
        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertEquals($payment->getStatus(), PaymentStatus::RECEIVED_IN_CASH());
        return $payment;
    }

    /**
     * @throws GuzzleException
     * @throws AsaasException
     */
    public function undoConfirmReceiveInCash(Payment $payment): ?Payment
    {
        $paymentId = $payment->getId();
        $paymentAsArray = $payment->toArray();
        $this->assertEquals($payment->getStatus(), PaymentStatus::RECEIVED_IN_CASH());
        if ($this->withMock === true) {
            $paymentAsArray['status'] = PaymentStatus::PENDING();
            $this->addInterceptor('post', "payments/$paymentId/undoReceivedInCash", $paymentAsArray);
        }
        $payment = $payment->undoConfirmReceiveInCash();
        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertEquals($payment->getStatus(), PaymentStatus::PENDING());
        return $payment;
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function uploadDocumentSuccess(Payment $payment): ?Payment
    {

        $testFile = Utils::baseDir('/tests/Files/sample.pdf');
        $tempDirectory = sys_get_temp_dir();
        $newFileName = 'file_' . uniqid('', true) . '.pdf';
        $destinationPath = $tempDirectory . DIRECTORY_SEPARATOR . $newFileName;
        copy($testFile, $destinationPath);

        $uploadData = [
            'file' => $destinationPath,
            'type' => DocumentType::INVOICE(),
            'availableAfterPayment' => true
        ];
        if ($this->withMock === true) {
            $expectedResponse = $this->getSuccessDocumentResponse($payment->getId());
            $this->addInterceptor('post', "payments/{$payment->getId()}/documents", $expectedResponse);
        }
        $result = $payment->uploadDocument(
            $uploadData['type'],
            $uploadData['file'],
            $uploadData['availableAfterPayment']
        );
        $this->assertInstanceOf(PaymentDocumentFile::class, $result->getFile());
        $this->assertInstanceOf(PaymentDocument::class, $result);
        $this->assertEquals(DocumentType::INVOICE(), $result->getType());
        return $payment;
    }

    /**
     * @throws Exception
     */
    private function getSuccessDocumentResponse($documentId): ?array
    {
        return Utils::getPaymentsJsonFile("{id}_documents.json", ['documentId' => $documentId]);
    }

    /**
     * @throws AsaasException
     * @throws Exception
     */
    public function getDocumentsList(Payment $payment): ?array
    {
        $expectedResponse = [
            "object" => "list",
            "hasMore" => false,
            "totalCount" => 2,
            "limit" => 10,
            "offset" => 0,
            "data" => [
                $this->getSuccessDocumentResponse($payment->getId()),
            ]
        ];

        if ($this->withMock === true) {
            $this->addInterceptor('get', "payments/{$payment->getId()}/documents", $expectedResponse);
        }

        $result = $payment->getDocuments();
        $this->assertInstanceOf(ListResponse::class, $result);

        foreach ($result->getRows() as $r) {
            $this->assertInstanceOf(PaymentDocument::class, $r);
        }
        return [$payment, $result->getRows()[0]];
    }

    /**
     * @throws Exception
     */
    public function updateDocuments(array $data): ?array
    {
        /**
         * @var Payment $payment
         ***/
        [$payment, $document] = $data;
        $documentId = $document->getId();
        $expectedResponse = $this->getSuccessDocumentResponse($payment->getId());

        $newType = DocumentType::MEDIA();
        if ($this->withMock === true) {
            $expectedResponse['type'] = $newType;
            $endpoint = "payments/{$payment->getId()}/documents/$documentId";
            $this->addInterceptor('put', $endpoint, $expectedResponse);
        }
        $result = $payment->updateDocument($documentId, $document->isAvailableAfterPayment(), $newType);
        $this->assertInstanceOf(PaymentDocument::class, $result);
        $this->assertEquals($newType, $result->getType());
        return $data;
    }

    /**
     * @throws Exception
     */
    public function deleteDocument(array $data): void
    {
        /**
         * @var Payment $payment
         * @var PaymentDocument $document
         ***/
        [$payment, $document] = $data;
        $documentId = $document->getId();

        if ($this->withMock === true) {
            $expectedResponse = [
                "deleted" => true,
                "id" => $documentId
            ];
            $endpoint = "payments/{$payment->getId()}/documents/$documentId";
            $this->addInterceptor('delete', $endpoint, $expectedResponse);
        }
        $result = $payment->deleteDocument($documentId);
        $this->assertTrue($result);
    }

    /**
     * @throws GuzzleException
     * @throws AsaasException
     */
    public function getPixQrCode(Payment $payment): ?Payment
    {
        if ($this->withMock === true) {
            $expectedResponse = Utils::getPaymentsJsonFile('{id}_pixQrCode.json');
            $this->addInterceptor("get", "payments/{$payment->getId()}/pixQrCode", $expectedResponse);
        }
        $qrCode = $payment->getPixQrCode();

        $this->assertInstanceOf(PixQrcode::class, $qrCode);
        return $payment;
    }

    /**
     * @throws Exception
     */
    public function getDocument(array $data): ?array
    {
        /**
         * @var Payment $payment
         * @var PaymentDocument $document
         ***/
        [$payment, $document] = $data;
        $documentId = $document->getId();
        $expectedResponse = $this->getSuccessDocumentResponse($payment->getId());

        $newType = DocumentType::MEDIA();
        if ($this->withMock === true) {
            $expectedResponse['type'] = $newType;
            $endpoint = "payments/{$payment->getId()}/documents/$documentId";
            $this->addInterceptor('get', $endpoint, $expectedResponse);
        }
        $result = $payment->getDocument($documentId);
        $this->assertInstanceOf(PaymentDocument::class, $result);
        $this->assertEquals($newType, $result->getType());
        return $data;
    }

    /**
     * @throws Exception
     */
    public function simulate(): void
    {
        $value = 100.00;
        $installmentCount = 2;
        $billingTypes = ['CREDIT_CARD', 'BANK_SLIP', 'PIX'];

        if ($this->withMock) {
            $expectedResponse = Utils::getPaymentsJsonFile('simulate.json');
            $this->addInterceptor("post", "payments/simulate", $expectedResponse);
        }

        $entityClass = $this->getModelClass();
        $response = call_user_func([$entityClass, 'simulate'], $value, $installmentCount, $billingTypes);

        $this->assertArrayHasKey('creditCard', $response);
        $this->assertInstanceOf(
            Installment::class,
            $response['creditCard']['installment']
        );
        $this->assertArrayHasKey('pix', $response);
        $this->assertInstanceOf(
            Installment::class,
            $response['pix']['installment']
        );

        if (isset($expectedResponse)) {
            $this->assertEquals($expectedResponse['value'], $response['value']);
            $this->assertEquals($expectedResponse['creditCard']['netValue'], $response['creditCard']['netValue']);
            $this->assertEquals(
                $expectedResponse['creditCard']['feePercentage'],
                $response['creditCard']['feePercentage']
            );
            $this->assertEquals(
                $expectedResponse['creditCard']['operationFee'],
                $response['creditCard']['operationFee']
            );
            $this->assertEquals($expectedResponse['bankSlip']['netValue'], $response['bankSlip']['netValue']);
            $this->assertEquals($expectedResponse['bankSlip']['feeValue'], $response['bankSlip']['feeValue']);
            $this->assertEquals($expectedResponse['pix']['netValue'], $response['pix']['netValue']);
            $this->assertEquals($expectedResponse['pix']['feePercentage'], $response['pix']['feePercentage']);
            $this->assertEquals($expectedResponse['pix']['feeValue'], $response['pix']['feeValue']);
            foreach (['creditCard', 'bankSlip', 'pix'] as $method) {
                $this->assertEquals(
                    $expectedResponse[$method]['installment']['paymentNetValue'],
                    $response[$method]['installment']->getPaymentNetValue()
                );
                $this->assertEquals(
                    $expectedResponse[$method]['installment']['paymentValue'],
                    $response[$method]['installment']->getPaymentValue()
                );
            }
        }
    }

    /**
     * @throws AsaasException|GuzzleException
     */
    public function getBillingInfo(Payment $payment): ?Payment
    {
        $paymentId = $payment->getId();

        if ($this->withMock) {
            $expectedResponse = Utils::getPaymentsJsonFile('{id}_billingInfo.json');

            $this->addInterceptor("get", "payments/$paymentId/billingInfo", $expectedResponse);
        }

        $response = $payment->getBillingInfo();

        $this->assertInstanceOf(BillingInfoResponse::class, $response);

        $this->assertNotNull($response->getPix());
        if (isset($expectedResponse)) {
            $this->assertEquals($expectedResponse['pix']['encodedImage'], $response->getPix()->getEncodedImage());
            $this->assertEquals($expectedResponse['pix']['expirationDate'], $response->getPix()->getExpirationDate());
        }

        if (isset($expectedResponse)) {
            $creditCard = $response->getCreditCard();
            $this->assertNotNull($creditCard);
            $this->assertEquals($expectedResponse['creditCard']['creditCardNumber'], $creditCard->getNumber());
            $this->assertEquals($expectedResponse['creditCard']['creditCardBrand'], $creditCard->getCreditCardBrand());
            $this->assertEquals($expectedResponse['creditCard']['creditCardToken'], $creditCard->getCreditCardToken());
        }
        $bankSlip = $response->getBankSlip();
        $this->assertNotNull($bankSlip);
        if (isset($expectedResponse)) {
            $this->assertEquals(
                $expectedResponse['bankSlip']['identificationField'],
                $bankSlip->getIdentificationField()
            );
            $this->assertEquals($expectedResponse['bankSlip']['nossoNumero'], $bankSlip->getNossoNumero());
            $this->assertEquals($expectedResponse['bankSlip']['barCode'], $bankSlip->getBarCode());
            $this->assertEquals($expectedResponse['bankSlip']['bankSlipUrl'], $bankSlip->getBankSlipUrl());
            $this->assertEquals(
                $expectedResponse['bankSlip']['daysAfterDueDateToRegistrationCancellation'],
                $bankSlip->getDaysAfterDueDateToRegistrationCancellation()
            );
        }

        return $payment;
    }

    /**
     * @throws AsaasException
     * @throws GuzzleException
     */
    public function getIdentificationField(Payment $payment): ?Payment
    {
        $paymentId = $payment->getId();
        $this->assertNotNull($payment->getId());
        $expectedResponse = [
            "identificationField" => "46191110000000000000010631800017710060000009587",
            "nossoNumero" => "10631800",
            "barCode" => "46197100600000095871110000000000001063180001"
        ];
        if ($this->withMock === true) {
            $this->addInterceptor("get", "payments/$paymentId/identificationField", $expectedResponse);
        }
        $response = $payment->getIdentificationField();
        foreach ($expectedResponse as $key => $value) {
            $this->assertArrayHasKey($key, $response);
        }
        return $payment;
    }

    public function getLimits(): void
    {

        $expectedResponse = Utils::getPaymentsJsonFile('limits.json');
        if ($this->withMock === true) {
            $this->addInterceptor("get", "payments/limits", $expectedResponse);
        }
        /**
         * @var PaymentLimitsResponse $response
         */
        $response = call_user_func([$this->getModelClass(), 'getLimits']);
        $this->assertInstanceOf(PaymentLimitsResponse::class, $response);
    }

    /**
     * @throws Exception
     */
    public function createPaymentWithCreditCardPreAuthorized(): Payment
    {
        $replaceData = $this->getRandomData();
        $replaceData['billingType'] = BillingType::CREDIT_CARD();
        $replaceData['remoteIp'] = $this->faker->ipv4;
        $replaceData['authorizeOnly'] = true;
        unset($replaceData['discount']);

        if ($this->withMock === true) {
            $replaceData['status'] = PaymentStatus::AUTHORIZED();
        }
        $replaceData = array_merge($replaceData, $this->generateCreditCardData());
        $createResponse = $this->processCreate($replaceData, "createWithCreditCard");

        $this->assertEquals(BillingType::CREDIT_CARD(), $createResponse->getBillingType());
        $this->assertEquals(PaymentStatus::AUTHORIZED(), $createResponse->getStatus());
        return $createResponse;
    }

    /**
     * @throws AsaasException
     * @throws GuzzleException
     * @throws Exception
     */
    public function bankSlipRefund(): Payment
    {
        $paymentId = getenv("ASAAS_PAID_SLIPBANK");
        if ($this->withMock === true) {
            $randomData = $this->getRandomData();
            $randomData['id'] = $paymentId;
            $randomData['status'] = PaymentStatus::CONFIRMED();
            $randomData['billingType'] = BillingType::BOLETO();
            $paymentObject = Payment::fromArray($randomData);
            $paymentAsArray = $paymentObject->toArray();
            $paymentAsArray['status'] = PaymentStatus::CONFIRMED();
            $this->addInterceptor("get", "payments/$paymentId", $paymentAsArray);
            $refundResponse = ['requestUrl' => 'https://sandbox.asaas.com/solicitar-estorno/37ij5mdxwo1234'];
            $this->addInterceptor("post", "payments/$paymentId/bankSlip/refund", $refundResponse);
        }
        $payment = Payment::getById($paymentId);
        $this->assertEquals($payment->getId(), $paymentId);
        $this->assertEquals(PaymentStatus::CONFIRMED(), $payment->getStatus());

        $refundResponse = $payment->bankSlipRefund();
        $this->assertNotNull($refundResponse);
        $this->assertTrue(filter_var($refundResponse, FILTER_VALIDATE_URL));
        return $payment;
    }

    /**
     * @throws AsaasException
     * @throws GuzzleException
     */
    public function captureAuthorizedPayment(Payment $payment): Payment
    {
        $paymentId = $payment->getId();
        $this->assertEquals(PaymentStatus::AUTHORIZED(), $payment->getStatus());
        if ($this->withMock === true) {
            $paymentAsArray = $payment->toArray();
            $paymentAsArray['status'] = PaymentStatus::CONFIRMED();
            $endpoint = "payments/$paymentId/captureAuthorizedPayment";
            $this->addInterceptor("post", $endpoint, $paymentAsArray);
        }
        $payment = $payment->captureAuthorizedPayment();
        $this->assertEquals(PaymentStatus::CONFIRMED(), $payment->getStatus());
        return $payment;
    }
}
