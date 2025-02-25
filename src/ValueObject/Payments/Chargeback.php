<?php

namespace AsaasPaymentGateway\ValueObject\Payments;

use AsaasPaymentGateway\ValueObject\ArrayableInterface;

class Chargeback implements ArrayableInterface
{
    private string $id;
    private string $payment;
    private ?string $installment;
    private string $customerAccount;
    private string $status;
    private string $reason;
    private string $disputeStartDate;
    private float $value;
    private string $paymentDate;
    private CreditCard $creditCard;
    private string $disputeStatus;
    private string $deadlineToSendDisputeDocuments;

    public function __construct(
        string $id,
        string $payment,
        string $customerAccount,
        string $status,
        string $reason,
        string $disputeStartDate,
        float $value,
        string $paymentDate,
        CreditCard $creditCard,
        string $disputeStatus,
        string $deadlineToSendDisputeDocuments,
        ?string $installment = null
    ) {
        $this->id = $id;
        $this->payment = $payment;
        $this->installment = $installment;
        $this->customerAccount = $customerAccount;
        $this->status = $status;
        $this->reason = $reason;
        $this->disputeStartDate = $disputeStartDate;
        $this->value = $value;
        $this->paymentDate = $paymentDate;
        $this->creditCard = $creditCard;
        $this->disputeStatus = $disputeStatus;
        $this->deadlineToSendDisputeDocuments = $deadlineToSendDisputeDocuments;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['payment'],
            $data['customerAccount'],
            $data['status'],
            $data['reason'],
            $data['disputeStartDate'],
            (float)$data['value'],
            $data['paymentDate'],
            CreditCard::fromArray($data['creditCard']),
            $data['disputeStatus'],
            $data['deadlineToSendDisputeDocuments'],
            $data['installment'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'payment' => $this->payment,
            'installment' => $this->installment,
            'customerAccount' => $this->customerAccount,
            'status' => $this->status,
            'reason' => $this->reason,
            'disputeStartDate' => $this->disputeStartDate,
            'value' => $this->value,
            'paymentDate' => $this->paymentDate,
            'creditCard' => $this->creditCard->toArray(),
            'disputeStatus' => $this->disputeStatus,
            'deadlineToSendDisputeDocuments' => $this->deadlineToSendDisputeDocuments,
        ];
    }
}
