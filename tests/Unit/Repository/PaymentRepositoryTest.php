<?php

namespace CaiqueMcz\AsaasPaymentGateway\Tests\Unit\Repository;

use CaiqueMcz\AsaasPaymentGateway\Enums\Payments\DocumentType;
use CaiqueMcz\AsaasPaymentGateway\Exception\AsaasException;
use CaiqueMcz\AsaasPaymentGateway\Helpers\Utils;
use CaiqueMcz\AsaasPaymentGateway\Http\Client;
use CaiqueMcz\AsaasPaymentGateway\Model\Payment;
use CaiqueMcz\AsaasPaymentGateway\Repository\PaymentRepository;
use CaiqueMcz\AsaasPaymentGateway\Response\ListResponse;
use CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments\PaymentDocument;
use CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments\PixQrcode;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PaymentRepositoryTest extends TestCase
{
    private PaymentRepository $repository;
    private MockObject $clientMock;
    private string $paymentId = 'pay_123456789';
    private string $documentId = 'doc_123456789';

    public function testCaptureAuthorizedPaymentReturnsPaymentModel(): void
    {
        $expectedResponse = [
            'id' => $this->paymentId,
            'customer' => 'cus_123', // Campo obrigatório
            'value' => 100.00,      // Campo obrigatório
            'billingType' => 'CREDIT_CARD', // Campo obrigatório
            'dueDate' => '2025-01-01', // Campo obrigatório
            'status' => 'CONFIRMED'
        ];

        $this->clientMock
            ->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo("payments/{$this->paymentId}/captureAuthorizedPayment"),
                $this->equalTo([])
            )
            ->willReturn($expectedResponse);

        $result = $this->repository->captureAuthorizedPayment($this->paymentId);

        $this->assertInstanceOf(Payment::class, $result);
        $this->assertNotNull($result->getId());
        $this->assertNotNull($result->getCustomer());
        $this->assertNotNull($result->getValue());
        $this->assertNotNull($result->getBillingType());
        $this->assertNotNull($result->getDueDate());
    }

    public function testPayWithCreditCardRequiresValidData(): void
    {
        $creditCardData = [
            'creditCardToken' => 'token_123',
            'creditCardHolderInfo' => [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'cpfCnpj' => '12345678901',
                'postalCode' => '12345678',
                'addressNumber' => '123',
                'addressComplement' => 'Apt 1',
                'phone' => '1234567890',
                'mobilePhone' => '1234567890'
            ]
        ];

        $expectedResponse = [
            'id' => $this->paymentId,
            'customer' => 'cus_123',
            'value' => 100.00,
            'billingType' => 'CREDIT_CARD',
            'dueDate' => '2025-01-01',
            'status' => 'CONFIRMED'
        ];

        $this->clientMock
            ->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo("payments/{$this->paymentId}/payWithCreditCard"),
                $this->equalTo($creditCardData)
            )
            ->willReturn($expectedResponse);

        $result = $this->repository->payWithCreditCard($this->paymentId, $creditCardData);

        $this->assertInstanceOf(Payment::class, $result);
    }

    public function testCreateWithCreditCardRequiresAllMandatoryFields(): void
    {
        $paymentData = [
            'customer' => 'cus_123',
            'billingType' => 'CREDIT_CARD',
            'value' => 100.00,
            'dueDate' => '2025-01-01',
            'creditCard' => [
                'holderName' => 'John Doe',
                'number' => '4111111111111111',
                'expiryMonth' => '12',
                'expiryYear' => '2025',
                'ccv' => '123'
            ],
            'creditCardHolderInfo' => [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'cpfCnpj' => '12345678901',
                'postalCode' => '12345678',
                'addressNumber' => '123',
                'phone' => '1234567890'
            ]
        ];

        $expectedResponse = array_merge($paymentData, ['id' => $this->paymentId]);

        $this->clientMock
            ->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo('payments'),
                $this->equalTo($paymentData)
            )
            ->willReturn($expectedResponse);

        $result = $this->repository->createWithCreditCard($paymentData);

        $this->assertInstanceOf(Payment::class, $result);
    }

    public function testGetViewingInfoReturnsArray(): void
    {
        $expectedResponse = [
            'invoiceViewedDate' => '2025-01-01',
            'boletoViewedDate' => '2025-01-01'
        ];

        $this->clientMock
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo("payments/{$this->paymentId}/viewingInfo"))
            ->willReturn($expectedResponse);

        $result = $this->repository->getViewingInfo($this->paymentId);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('invoiceViewedDate', $result);
        $this->assertArrayHasKey('boletoViewedDate', $result);
    }

    public function testGetPixQrCodeReturnsPixQrcodeObject(): void
    {
        $expectedResponse = [
            'encodedImage' => 'base64_image',
            'payload' => 'pix_payload',
            'expirationDate' => '2025-01-01'
        ];

        $this->clientMock
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo("payments/{$this->paymentId}/pixQrCode"))
            ->willReturn($expectedResponse);

        $result = $this->repository->getPixQrCode($this->paymentId);

        $this->assertInstanceOf(PixQrcode::class, $result);
        $this->assertNotEmpty($result->getEncodedImage());
        $this->assertNotEmpty($result->getPayload());
        $this->assertNotEmpty($result->getExpirationDate());
    }

    public function testConfirmReceiveInCashRequiresValidData(): void
    {
        $confirmData = [
            'paymentDate' => '2025-01-01',
            'value' => 100.00,
            'notifyCustomer' => true
        ];

        $expectedResponse = [
            'id' => $this->paymentId,
            'customer' => 'cus_123',
            'value' => 100.00,
            'billingType' => 'UNDEFINED',
            'dueDate' => '2025-01-01',
            'status' => 'RECEIVED_IN_CASH'
        ];

        $this->clientMock
            ->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo("payments/{$this->paymentId}/receiveInCash"),
                $this->equalTo($confirmData)
            )
            ->willReturn($expectedResponse);

        $result = $this->repository->confirmReceiveInCash($this->paymentId, $confirmData);

        $this->assertInstanceOf(Payment::class, $result);
    }

    public function testSimulateRequiresValidValue(): void
    {
        $value = 1000.00;
        $installmentCount = 12;
        $billingTypes = ['CREDIT_CARD', 'BOLETO'];

        $expectedResponse = [
            'installments' => [
                [
                    'installmentCount' => 1,
                    'value' => 1000.00,
                    'totalValue' => 1000.00
                ],
                [
                    'installmentCount' => 12,
                    'value' => 90.00,
                    'totalValue' => 1080.00
                ]
            ]
        ];

        $requestData = [
            'value' => $value,
            'installmentCount' => $installmentCount,
            'billingTypes' => $billingTypes
        ];

        $this->clientMock
            ->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo("payments/simulate"),
                $this->equalTo($requestData)
            )
            ->willReturn($expectedResponse);

        $result = $this->repository->simulate($value, $installmentCount, $billingTypes);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('installments', $result);
        $this->assertIsArray($result['installments']);

        foreach ($result['installments'] as $installment) {
            $this->assertArrayHasKey('installmentCount', $installment);
            $this->assertArrayHasKey('value', $installment);
            $this->assertArrayHasKey('totalValue', $installment);
            $this->assertIsInt($installment['installmentCount']);
            $this->assertIsFloat($installment['value']);
            $this->assertIsFloat($installment['totalValue']);
        }
    }

    public function testGetIdentificationFieldReturnsCorrectFormat(): void
    {
        $expectedResponse = [
            'identificationField' => '34191.75124 34567.871234 51234.567891 8 12345678901234',
            'nossoNumero' => '123456789',
            'barCode' => '34198123456789012345678901234567890123456789'
        ];

        $this->clientMock
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo("payments/{$this->paymentId}/identificationField"))
            ->willReturn($expectedResponse);

        $result = $this->repository->getIdentificationField($this->paymentId);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('identificationField', $result);
        $this->assertArrayHasKey('nossoNumero', $result);
        $this->assertArrayHasKey('barCode', $result);
        $this->assertIsString($result['identificationField']);
        $this->assertIsString($result['nossoNumero']);
        $this->assertIsString($result['barCode']);
    }

    public function testRefundRequiresValidData(): void
    {
        $refundData = [
            'value' => 100.00,
            'description' => 'Test refund'
        ];

        $expectedResponse = [
            'id' => $this->paymentId,
            'customer' => 'cus_123',
            'value' => 100.00,
            'billingType' => 'CREDIT_CARD',
            'dueDate' => '2025-01-01',
            'status' => 'REFUNDED'
        ];

        $this->clientMock
            ->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo("payments/{$this->paymentId}/refund"),
                $this->equalTo($refundData)
            )
            ->willReturn($expectedResponse);

        $result = $this->repository->refund($this->paymentId, $refundData);

        $this->assertInstanceOf(Payment::class, $result);
        $this->assertEquals('REFUNDED', $result->getStatus());
        $this->assertEquals(100.00, $result->getValue());
    }

    public function testUploadDocumentSuccess(): void
    {
        $testFile = sys_get_temp_dir() . '/test_file.pdf';
        file_put_contents($testFile, 'test content');

        $uploadData = [
            'file' => $testFile,
            'type' => 'INVOICE',
            'availableAfterPayment' => true
        ];

        $expectedResponse = [
            'object' => 'document',
            'id' => $this->documentId,
            'name' => 'test_file.pdf',
            'type' => 'INVOICE',
            'availableAfterPayment' => true,
            'file' => [
                'publicId' => 'pub_123',
                'originalName' => 'test_file.pdf',
                'size' => 123,
                'extension' => 'pdf',
                'previewUrl' => 'https://example.com/preview',
                'downloadUrl' => 'https://example.com/download'
            ],
            'deleted' => false
        ];

        $this->clientMock
            ->expects($this->once())
            ->method('postWithFile')
            ->with(
                $this->equalTo("payments/{$this->paymentId}/documents"),
                $this->equalTo($uploadData)
            )
            ->willReturn($expectedResponse);

        $result = $this->repository->uploadDocument($this->paymentId, $uploadData);

        $this->assertInstanceOf(PaymentDocument::class, $result);
        $this->assertEquals($this->documentId, $result->getId());
        $this->assertEquals('INVOICE', $result->getType());
        $this->assertTrue($result->isAvailableAfterPayment());

        unlink($testFile);
    }

    public function testUploadDocumentThrowsExceptionWhenFileNotFound(): void
    {
        $this->expectException(AsaasException::class);
        $this->expectExceptionMessage('File not found');

        $uploadData = [
            'file' => '/non/existent/file.pdf',
            'type' => 'INVOICE'
        ];

        $this->repository->uploadDocument($this->paymentId, $uploadData);
    }

    public function testGetDocumentsSuccess(): void
    {
        $expectedResponse = [
            'object' => 'list',
            'hasMore' => false,
            'totalCount' => 1,
            'limit' => 10,
            'offset' => 0,
            'data' => [
                $this->getSuccessDocumentResponse()
            ]
        ];

        $this->clientMock
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo("payments/{$this->paymentId}/documents"))
            ->willReturn($expectedResponse);

        $result = $this->repository->getDocuments($this->paymentId);

        $this->assertInstanceOf(ListResponse::class, $result);
        $this->assertEquals(1, $result->getTotalCount());
        $this->assertCount(1, $result->getRows());
        $this->assertInstanceOf(PaymentDocument::class, $result->getRows()[0]);
    }

    /**
     * @throws \Exception
     */
    private function getSuccessDocumentResponse(): ?array
    {
        return Utils::getJsonFile("tests/Mocks/payments/{id}_documents.json", ['documentId' => $this->documentId]);
    }

    /**
     * @throws AsaasException
     */
    public function testUpdateDocumentSuccess(): void
    {
        $updateData = [
            'type' => 'CONTRACT',
            'availableAfterPayment' => false
        ];
        $expectedResponse = $this->getSuccessDocumentResponse();
        $expectedResponse['type'] = 'CONTRACT';
        $expectedResponse['availableAfterPayment'] = false;

        $this->clientMock
            ->expects($this->once())
            ->method('put')
            ->with(
                $this->equalTo("payments/{$this->paymentId}/documents/{$this->documentId}"),
                $this->equalTo($updateData)
            )
            ->willReturn($expectedResponse);

        $result = $this->repository->updateDocument($this->paymentId, $this->documentId, $updateData);

        $this->assertInstanceOf(PaymentDocument::class, $result);
        $this->assertEquals('CONTRACT', $result->getType());
        $this->assertFalse($result->isAvailableAfterPayment());
    }

    public function testGetDocumentSuccess(): void
    {
        $expectedResponse = $this->getSuccessDocumentResponse();
        $this->clientMock
            ->expects($this->once())
            ->method('get')
            ->with(
                $this->equalTo("payments/{$this->paymentId}/documents/{$this->documentId}"),
                $this->equalTo([])
            )
            ->willReturn($expectedResponse);

        $result = $this->repository->getDocument($this->paymentId, $this->documentId);

        $this->assertInstanceOf(PaymentDocument::class, $result);
        $this->assertEquals($this->documentId, $result->getId());
        $this->assertEquals('INVOICE', $result->getType());
        $this->assertTrue($result->isAvailableAfterPayment());
    }

    public function testDeleteDocumentSuccess(): void
    {
        $expectedResponse = ['deleted' => true];

        $this->clientMock
            ->expects($this->once())
            ->method('delete')
            ->with(
                $this->equalTo("payments/{$this->paymentId}/documents/{$this->documentId}"),
                $this->equalTo([])
            )
            ->willReturn($expectedResponse);

        $result = $this->repository->deleteDocument($this->paymentId, $this->documentId);

        $this->assertTrue($result);
    }

    public function testDeleteDocumentReturnsFalseWhenNotDeleted(): void
    {
        $expectedResponse = ['deleted' => false];

        $this->clientMock
            ->expects($this->once())
            ->method('delete')
            ->willReturn($expectedResponse);

        $result = $this->repository->deleteDocument($this->paymentId, $this->documentId);

        $this->assertFalse($result);
    }

    protected function setUp(): void
    {
        $this->clientMock = $this->createMock(Client::class);
        $this->repository = new PaymentRepository(Payment::class);

        $reflection = new \ReflectionClass($this->repository);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($this->repository, $this->clientMock);
    }
}
