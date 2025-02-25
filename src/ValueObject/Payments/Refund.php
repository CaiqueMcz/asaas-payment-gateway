<?php

namespace AsaasPaymentGateway\ValueObject\Payments;

use AsaasPaymentGateway\Enums\Payments\RefundStatus;
use AsaasPaymentGateway\ValueObject\ArrayableInterface;

class Refund implements ArrayableInterface
{
    private string $dateCreated;
    private ?RefundStatus $status;
    private float $value;
    private ?string $endToEndIdentifier;
    private ?string $description;
    private ?string $effectiveDate;
    private ?string $transactionReceiptUrl;
    private array $refundedSplits;
    private ?string $paymentId;

    public function __construct(
        string $dateCreated,
        ?RefundStatus $status,
        float $value,
        ?string $endToEndIdentifier = null,
        ?string $description = null,
        ?string $effectiveDate = null,
        ?string $transactionReceiptUrl = null,
        array $refundedSplits = [],
        ?string $paymentId = null
    ) {
        $this->dateCreated = $dateCreated;
        $this->status = $status;
        $this->value = $value;
        $this->endToEndIdentifier = $endToEndIdentifier;
        $this->description = $description;
        $this->effectiveDate = $effectiveDate;
        $this->transactionReceiptUrl = $transactionReceiptUrl;
        $this->refundedSplits = $refundedSplits;
        $this->paymentId = $paymentId;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['dateCreated']) ? (string)$data['dateCreated'] : '',
            isset($data['status']) ? RefundStatus::from($data['status']) : null,
            isset($data['value']) ? (float)$data['value'] : 0.0,
            isset($data['endToEndIdentifier']) ? (string)$data['endToEndIdentifier'] : null,
            isset($data['description']) ? (string)$data['description'] : null,
            isset($data['effectiveDate']) ? (string)$data['effectiveDate'] : null,
            isset($data['transactionReceiptUrl']) ? (string)$data['transactionReceiptUrl'] : null,
            isset($data['refundedSplits']) && is_array($data['refundedSplits']) ? $data['refundedSplits'] : [],
            isset($data['paymentId']) ? (string)$data['paymentId'] : null
        );
    }

    public function toArray(): array
    {
        return [
            'dateCreated' => $this->dateCreated,
            'status' => (string)$this->status,
            'value' => $this->value,
            'endToEndIdentifier' => $this->endToEndIdentifier,
            'description' => $this->description,
            'effectiveDate' => $this->effectiveDate,
            'transactionReceiptUrl' => $this->transactionReceiptUrl,
            'refundedSplits' => $this->refundedSplits,
            'paymentId' => $this->paymentId,
        ];
    }

    public function getDateCreated(): string
    {
        return $this->dateCreated;
    }

    public function getStatus(): ?RefundStatus
    {
        return $this->status;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function getEndToEndIdentifier(): ?string
    {
        return $this->endToEndIdentifier;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getEffectiveDate(): ?string
    {
        return $this->effectiveDate;
    }

    public function getTransactionReceiptUrl(): ?string
    {
        return $this->transactionReceiptUrl;
    }

    public function getRefundedSplits(): array
    {
        return $this->refundedSplits;
    }

    public function getPaymentId(): ?string
    {
        return $this->paymentId;
    }
}
