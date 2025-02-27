<?php

namespace CaiqueMcz\AsaasPaymentGateway\Response;

use CaiqueMcz\AsaasPaymentGateway\ValueObject\ArrayableInterface;
use CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments\BankSlip;
use CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments\CreditCard;
use CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments\PixQrcode;

class BillingInfoResponse implements ArrayableInterface
{
    private ?PixQrcode $pix;
    private ?CreditCard $creditCard;
    private ?BankSlip $bankSlip;

    public function __construct(
        ?PixQrcode $pix = null,
        ?CreditCard $creditCard = null,
        ?BankSlip $bankSlip = null
    ) {
        $this->pix = $pix;
        $this->creditCard = $creditCard;
        $this->bankSlip = $bankSlip;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['pix']) ? PixQrcode::fromArray($data['pix']) : null,
            isset($data['creditCard']) ? CreditCard::fromArray($data['creditCard']) : null,
            isset($data['bankSlip']) ? BankSlip::fromArray($data['bankSlip']) : null
        );
    }

    public function toArray(): array
    {
        $data = [];

        if ($this->pix) {
            $data['pix'] = $this->pix->toArray();
        }

        if ($this->creditCard) {
            $data['creditCard'] = $this->creditCard->toArray();
        }

        if ($this->bankSlip) {
            $data['bankSlip'] = $this->bankSlip->toArray();
        }

        return $data;
    }

    public function getPix(): ?PixQrcode
    {
        return $this->pix;
    }

    public function getCreditCard(): ?CreditCard
    {
        return $this->creditCard;
    }

    public function getBankSlip(): ?BankSlip
    {
        return $this->bankSlip;
    }
}
