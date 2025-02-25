<?php

namespace AsaasPaymentGateway\Tests\Unit\ValueObject\Payments;

use AsaasPaymentGateway\ValueObject\Payments\PaymentDocument;
use AsaasPaymentGateway\ValueObject\Payments\PaymentDocumentFile;
use PHPUnit\Framework\TestCase;

class PaymentDocumentTest extends TestCase
{
    private array $validFileData;
    private array $validDocumentData;

    protected function setUp(): void
    {
        $this->validFileData = [
            'publicId' => 'TSrLvzPGF7HPQhYu9OZhZSBX3mm1sxpToEcFm30imOM3sKEjhzCc1zAIuqQ7n11',
            'originalName' => 'Nota Fiscal.pdf',
            'size' => 14499,
            'extension' => 'pdf',
            'previewUrl' => 'https://www.asaas.com/file/preview/TSrLvzPGF7HzAIuqQ7n11',
            'downloadUrl' => 'https://www.asaas.com/file/public/download/TSsKEjhzCc1zAIuqQ7n11'
        ];

        $this->validDocumentData = [
            'object' => 'paymentDocument',
            'id' => '609a3f98-8db7-4a89-b511-de4c3be6d462',
            'name' => 'Nota Fiscal.pdf',
            'type' => 'INVOICE',
            'availableAfterPayment' => true,
            'file' => $this->validFileData,
            'deleted' => false
        ];
    }

    public function testCreatePaymentDocumentFile(): void
    {
        $file = PaymentDocumentFile::fromArray($this->validFileData);

        $this->assertEquals($this->validFileData['publicId'], $file->getPublicId());
        $this->assertEquals($this->validFileData['originalName'], $file->getOriginalName());
        $this->assertEquals($this->validFileData['size'], $file->getSize());
        $this->assertEquals($this->validFileData['extension'], $file->getExtension());
        $this->assertEquals($this->validFileData['previewUrl'], $file->getPreviewUrl());
        $this->assertEquals($this->validFileData['downloadUrl'], $file->getDownloadUrl());
    }

    public function testCreatePaymentDocument(): void
    {
        $document = PaymentDocument::fromArray($this->validDocumentData);

        $this->assertEquals($this->validDocumentData['object'], $document->getObject());
        $this->assertEquals($this->validDocumentData['id'], $document->getId());
        $this->assertEquals($this->validDocumentData['name'], $document->getName());
        $this->assertEquals($this->validDocumentData['type'], $document->getType());
        $this->assertEquals($this->validDocumentData['availableAfterPayment'], $document->isAvailableAfterPayment());
        $this->assertInstanceOf(PaymentDocumentFile::class, $document->getFile());
        $this->assertEquals($this->validDocumentData['deleted'], $document->isDeleted());
    }

    public function testPaymentDocumentFileToArray(): void
    {
        $file = PaymentDocumentFile::fromArray($this->validFileData);
        $array = $file->toArray();

        $this->assertEquals($this->validFileData, $array);
    }

    public function testPaymentDocumentToArray(): void
    {
        $document = PaymentDocument::fromArray($this->validDocumentData);
        $array = $document->toArray();

        $this->assertEquals($this->validDocumentData, $array);
    }

    public function testPaymentDocumentWithDefaultValues(): void
    {
        $data = $this->validDocumentData;
        unset($data['deleted']);
        unset($data['object']);

        $document = PaymentDocument::fromArray($data);

        $this->assertEquals('paymentDocument', $document->getObject());
        $this->assertFalse($document->isDeleted());
    }

    public function testFileTypeCasting(): void
    {
        $data = $this->validFileData;
        $data['size'] = '14499';

        $file = PaymentDocumentFile::fromArray($data);

        $this->assertIsInt($file->getSize());
        $this->assertEquals(14499, $file->getSize());
    }

    public function testDocumentBooleanCasting(): void
    {
        $data = $this->validDocumentData;
        $data['availableAfterPayment'] = 1;
        $data['deleted'] = 0;

        $document = PaymentDocument::fromArray($data);

        $this->assertIsBool($document->isAvailableAfterPayment());
        $this->assertTrue($document->isAvailableAfterPayment());
        $this->assertIsBool($document->isDeleted());
        $this->assertFalse($document->isDeleted());
    }
}
