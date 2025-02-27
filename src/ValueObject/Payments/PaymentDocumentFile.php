<?php

namespace CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments;

use CaiqueMcz\AsaasPaymentGateway\ValueObject\ArrayableInterface;

class PaymentDocumentFile implements ArrayableInterface
{
    private string $publicId;
    private string $originalName;
    private int $size;
    private string $extension;
    private string $previewUrl;
    private string $downloadUrl;

    public function __construct(
        string $publicId,
        string $originalName,
        int $size,
        string $extension,
        string $previewUrl,
        string $downloadUrl
    ) {
        $this->publicId = $publicId;
        $this->originalName = $originalName;
        $this->size = $size;
        $this->extension = $extension;
        $this->previewUrl = $previewUrl;
        $this->downloadUrl = $downloadUrl;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['publicId'],
            $data['originalName'],
            (int)$data['size'],
            $data['extension'],
            $data['previewUrl'],
            $data['downloadUrl']
        );
    }

    public function toArray(): array
    {
        return [
            'publicId' => $this->publicId,
            'originalName' => $this->originalName,
            'size' => $this->size,
            'extension' => $this->extension,
            'previewUrl' => $this->previewUrl,
            'downloadUrl' => $this->downloadUrl
        ];
    }

    public function getPublicId(): string
    {
        return $this->publicId;
    }

    public function getOriginalName(): string
    {
        return $this->originalName;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getExtension(): string
    {
        return $this->extension;
    }

    public function getPreviewUrl(): string
    {
        return $this->previewUrl;
    }

    public function getDownloadUrl(): string
    {
        return $this->downloadUrl;
    }
}
