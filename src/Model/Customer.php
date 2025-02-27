<?php

namespace CaiqueMcz\AsaasPaymentGateway\Model;

use CaiqueMcz\AsaasPaymentGateway\Exception\AsaasException;
use CaiqueMcz\AsaasPaymentGateway\Helpers\CDate;
use CaiqueMcz\AsaasPaymentGateway\Traits\Model\CreateAbleTrait;
use CaiqueMcz\AsaasPaymentGateway\Traits\Model\DeleteAbleTrait;
use CaiqueMcz\AsaasPaymentGateway\Traits\Model\RestoreAbleTrait;
use CaiqueMcz\AsaasPaymentGateway\Traits\Model\UpdateAbleTrait;
use CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments\CreditCard as CreditCardValueObject;
use CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments\CreditCardHolderInfo;

/**
 * Métodos mágicos para acessar atributos dinamicamente:
 *
 * @method int|null    getId()
 * @method self        setId(int|null $id)
 * @method string      getName()
 * @method self        setName(string $name)
 * @method string      getCpfCnpj()
 * @method self        setCpfCnpj(string $cpfCnpj)
 * @method string|null getEmail()
 * @method self        setEmail(?string $email)
 * @method string|null getPhone()
 * @method self        setPhone(?string $phone)
 * @method string|null getMobilePhone()
 * @method self        setMobilePhone(?string $mobilePhone)
 * @method string|null getAddress()
 * @method self        setAddress(?string $address)
 * @method string|null getAddressNumber()
 * @method self        setAddressNumber(?string $addressNumber)
 * @method string|null getComplement()
 * @method self        setComplement(?string $complement)
 * @method string|null getProvince()
 * @method self        setProvince(?string $province)
 * @method string|null getPostalCode()
 * @method self        setPostalCode(?string $postalCode)
 * @method string|null getExternalReference()
 * @method self        setExternalReference(?string $externalReference)
 * @method bool|null   isNotificationDisabled()
 * @method self        setIsNotificationDisabled(?bool $notificationDisabled)
 * @method string|null getAdditionalEmails()
 * @method self        setAdditionalEmails(?string $additionalEmails)
 * @method string|null getMunicipalInscription()
 * @method self        setMunicipalInscription(?string $municipalInscription)
 * @method string|null getStateInscription()
 * @method self        setStateInscription(?string $stateInscription)
 * @method string|null getObservations()
 * @method self        setObservations(?string $observations)
 * @method string|null getGroupName()
 * @method self        setGroupName(?string $groupName)
 * @method string|null getCompany()
 * @method self        setCompany(?string $company)
 * @method bool|null   isForeignCustomer()
 * @method self        setIsForeignCustomer(?bool $foreignCustomer)
 * @method CDate|null  getDateCreated() Data de criação
 * @method self        setDateCreated(CDate $dateCreated)
 * @method string|null getCity()
 * @method self        setCity(?string $city)
 * @method string|null getCityName()
 * @method self        setCityName(?string $cityName)
 * @method string|null getState()
 * @method self        setState(?string $state)
 * @method string|null getCountry()
 * @method self        setCountry(?string $country)
 * @method string|null getPersonType()
 * @method self        setPersonType(?string $personType)
 * @method bool|null   isDeleted()
 * @method self        setIsDeleted(?bool $deleted)
 * @method self  save()
 * @method self  create(array $data)
 * @method self  update(array $data)
 * @method self  restore()
 * @method bool  delete()
 */
class Customer extends AbstractModel
{
    use CreateAbleTrait;
    use UpdateAbleTrait;
    use RestoreAbleTrait;
    use DeleteAbleTrait;

    protected array $fields = [
        'id',
        'name',
        'cpfCnpj',
        'email',
        'phone',
        'mobilePhone',
        'address',
        'addressNumber',
        'complement',
        'province',
        'postalCode',
        'externalReference',
        'notificationDisabled',
        'additionalEmails',
        'municipalInscription',
        'stateInscription',
        'observations',
        'groupName',
        'company',
        'foreignCustomer',
        'city',
        'cityName',
        'state',
        'country',
        'personType',
        'deleted'
    ];

    protected array $casts = [
        'foreignCustomer' => 'bool',
        'notificationDisabled' => 'bool',
        'deleted' => 'bool',
        'dateCreated' => 'date'
    ];

    /**
     * @throws AsaasException
     */
    public function tokenizeCreditCard(
        CreditCardValueObject $creditCard,
        CreditCardHolderInfo $creditCardHolderInfo,
        string $remoteIp
    ): ?AbstractModel {
        $creditCardData = [];
        $creditCardData['customer'] = $this->getId();
        $creditCardData['creditCard'] = $creditCard;
        $creditCardData['creditCardHolderInfo'] = $creditCardHolderInfo;
        $creditCardData['remoteIp'] = $remoteIp;
        return CreditCard::fromArray($creditCardData)->tokenizeCreditCard();
    }
}
