<?php

namespace CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments;

use CaiqueMcz\AsaasPaymentGateway\ValueObject\ArrayableInterface;

class BankSlip implements ArrayableInterface
{
    private string $identificationField;
    private string $nossoNumero;
    private string $barCode;
    private string $bankSlipUrl;
    private int $daysAfterDueDateToRegistrationCancellation;

    public function __construct(
        string $identificationField,
        string $nossoNumero,
        string $barCode,
        string $bankSlipUrl,
        int $daysAfterDueDateToRegistrationCancellation
    ) {
        $this->identificationField = $identificationField;
        $this->nossoNumero = $nossoNumero;
        $this->barCode = $barCode;
        $this->bankSlipUrl = $bankSlipUrl;
        $this->daysAfterDueDateToRegistrationCancellation = $daysAfterDueDateToRegistrationCancellation;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['identificationField'] ?? '',
            $data['nossoNumero'] ?? '',
            $data['barCode'] ?? '',
            $data['bankSlipUrl'] ?? '',
            isset($data['daysAfterDueDateToRegistrationCancellation']) ?
                (int)$data['daysAfterDueDateToRegistrationCancellation'] : 0
        );
    }

    public function toArray(): array
    {
        return [
            'identificationField' => $this->identificationField,
            'nossoNumero' => $this->nossoNumero,
            'barCode' => $this->barCode,
            'bankSlipUrl' => $this->bankSlipUrl,
            'daysAfterDueDateToRegistrationCancellation' => $this->daysAfterDueDateToRegistrationCancellation,
        ];
    }

    public function getIdentificationField(): string
    {
        return $this->identificationField;
    }

    public function getNossoNumero(): string
    {
        return $this->nossoNumero;
    }

    public function getBarCode(): string
    {
        return $this->barCode;
    }

    public function getBankSlipUrl(): string
    {
        return $this->bankSlipUrl;
    }

    public function getDaysAfterDueDateToRegistrationCancellation(): int
    {
        return $this->daysAfterDueDateToRegistrationCancellation;
    }
}
