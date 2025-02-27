<?php

namespace CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments;

use CaiqueMcz\AsaasPaymentGateway\ValueObject\ArrayableInterface;

class PaymentDocument implements ArrayableInterface
{
    private string $object;
    private string $id;
    private string $name;
    private string $type;
    private bool $availableAfterPayment;
    private PaymentDocumentFile $file;
    private bool $deleted;

    public function __construct(
        string $id,
        string $name,
        string $type,
        bool $availableAfterPayment,
        PaymentDocumentFile $file,
        bool $deleted = false,
        string $object = 'paymentDocument'
    ) {
        $this->object = $object;
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->availableAfterPayment = $availableAfterPayment;
        $this->file = $file;
        $this->deleted = $deleted;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['name'],
            $data['type'],
            (bool)$data['availableAfterPayment'],
            PaymentDocumentFile::fromArray($data['file']),
            (bool)($data['deleted'] ?? false),
            $data['object'] ?? 'paymentDocument'
        );
    }

    public function toArray(): array
    {
        return [
            'object' => $this->object,
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'availableAfterPayment' => $this->availableAfterPayment,
            'file' => $this->file->toArray(),
            'deleted' => $this->deleted
        ];
    }

    public function getObject(): string
    {
        return $this->object;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function isAvailableAfterPayment(): bool
    {
        return $this->availableAfterPayment;
    }

    public function getFile(): ?PaymentDocumentFile
    {
        return $this->file;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }
}
