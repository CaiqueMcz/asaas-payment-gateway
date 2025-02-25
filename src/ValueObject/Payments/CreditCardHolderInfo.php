<?php

namespace AsaasPaymentGateway\ValueObject\Payments;

use AsaasPaymentGateway\ValueObject\ArrayableInterface;

class CreditCardHolderInfo implements ArrayableInterface
{
    private string $name;
    private string $email;
    private string $cpfCnpj;
    private string $postalCode;
    private string $addressNumber;
    private string $addressComplement;
    private string $phone;
    private string $mobilePhone;

    public function __construct(
        string $name,
        string $email,
        string $cpfCnpj,
        string $postalCode,
        string $addressNumber,
        string $addressComplement,
        string $phone,
        string $mobilePhone
    ) {
        $this->name = $name;
        $this->email = $email;
        $this->cpfCnpj = $cpfCnpj;
        $this->postalCode = $postalCode;
        $this->addressNumber = $addressNumber;
        $this->addressComplement = $addressComplement;
        $this->phone = $phone;
        $this->mobilePhone = $mobilePhone;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'] ?? '',
            $data['email'] ?? '',
            $data['cpfCnpj'] ?? '',
            $data['postalCode'] ?? '',
            $data['addressNumber'] ?? '',
            $data['addressComplement'] ?? '',
            $data['phone'] ?? '',
            $data['mobilePhone'] ?? ''
        );
    }

    public function toArray(): array
    {
        return [
            'name'              => $this->name,
            'email'             => $this->email,
            'cpfCnpj'           => $this->cpfCnpj,
            'postalCode'        => $this->postalCode,
            'addressNumber'     => $this->addressNumber,
            'addressComplement' => $this->addressComplement,
            'phone'             => $this->phone,
            'mobilePhone'       => $this->mobilePhone,
        ];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getCpfCnpj(): string
    {
        return $this->cpfCnpj;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function getAddressNumber(): string
    {
        return $this->addressNumber;
    }

    public function getAddressComplement(): string
    {
        return $this->addressComplement;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getMobilePhone(): string
    {
        return $this->mobilePhone;
    }
}
