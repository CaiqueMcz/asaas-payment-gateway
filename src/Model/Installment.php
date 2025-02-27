<?php

namespace CaiqueMcz\AsaasPaymentGateway\Model;

use CaiqueMcz\AsaasPaymentGateway\Exception\AsaasException;
use CaiqueMcz\AsaasPaymentGateway\Helpers\CDate;
use CaiqueMcz\AsaasPaymentGateway\Repository\InstallmentRepository;
use CaiqueMcz\AsaasPaymentGateway\Response\ListResponse;
use CaiqueMcz\AsaasPaymentGateway\Traits\Model\CreateAbleTrait;
use CaiqueMcz\AsaasPaymentGateway\Traits\Model\DeleteAbleTrait;
use CaiqueMcz\AsaasPaymentGateway\Traits\Model\RestoreAbleTrait;
use CaiqueMcz\AsaasPaymentGateway\Traits\Model\UpdateAbleTrait;
use CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments\CreditCardHolderInfo;
use CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments\Discount;
use CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments\Fine;
use CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments\Interest;
use CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments\RefundList;
use CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments\SplitList;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Installment Model
 * @method string|null getId() ID
 * @method string|null setId(string $id) ID
 * @method CDate|null  getDateCreated() Data de criação
 * @method self setDateCreated(CDate $dateCreated)
 * @method self setInstallmentCount(int $installmentCount)
 * @method string getCustomer() Identificador único do cliente no Asaas
 * @method self setCustomer(string $customer)
 * @method float getValue() Valor de cada parcela
 * @method self setValue(float $value)
 * @method float|null getTotalValue() Valor total do parcelamento
 * @method self setTotalValue(float $totalValue)
 * @method string getBillingType() Forma de pagamento
 * @method self setBillingType(string $billingType)
 * @method CDate|null getDueDate() Data de vencimento
 * @method self setDueDate(CDate $dueDate)
 * @method string|null getDescription() Descrição do parcelamento
 * @method self setDescription(string $description)
 * @method bool|null isPostalService() Define se a cobrança será enviada via Correios
 * @method self setIsPostalService(bool $postalService)
 * @method int|null getDaysAfterDueDateToRegistrationCancellation() Dias após o vencimento para cancelamento do registro
 * @method self setDaysAfterDueDateToRegistrationCancellation(int $days)
 * @method string|null getPaymentExternalReference() Campo livre para busca
 * @method self setPaymentExternalReference(string $paymentExternalReference)
 * @method Discount|null getDiscount() Informações de desconto
 * @method self setDiscount(Discount $discount)
 * @method Interest|null getInterest() Informações de juros
 * @method self setInterest(Interest $interest)
 * @method Fine|null getFine() Informações de multa
 * @method self setFine(Fine $fine)
 * @method SplitList|null getSplits() Configurações do split
 * @method self setSplits(SplitList $splits)
 * @method static InstallmentRepository getRepository()
 * @method \AsaasPaymentGateway\ValueObject\Payments\CreditCard getCreditCard()
 * @method static self  create(array $data)
 * @method self  update(array $data)
 * @method self  restore()
 * @method bool  delete()
 * @method RefundList|null  getRefunds()
 * @method static self fromArray(array $data)
 * @method static self getById(string $id)
 * @method self|null refresh()
 * @method self|null save()
 */
class Installment extends AbstractModel
{
    use CreateAbleTrait;
    use UpdateAbleTrait;
    use RestoreAbleTrait;
    use DeleteAbleTrait;

    protected array $fields = [
        'id',
        'dateCreated',
        'installmentCount',
        'customer',
        'value',
        'totalValue',
        'billingType',
        'dueDate',
        'description',
        'postalService',
        'daysAfterDueDateToRegistrationCancellation',
        'paymentExternalReference',
        'discount',
        'interest',
        'fine',
        'splits',
        'creditCard',
        'creditCardToken',
        'creditCardHolderInfo',
        'authorizeOnly',
        'remoteIp',
        'refunds'
    ];

    protected array $requiredFields = [
        'installmentCount',
        'customer',
        'value',
        'billingType'
    ];

    protected array $casts = [
        'installmentCount' => 'int',
        'value' => 'float',
        'totalValue' => 'float',
        'postalService' => 'bool',
        'daysAfterDueDateToRegistrationCancellation' => 'int',
        'discount' => Discount::class,
        'interest' => Interest::class,
        'fine' => Fine::class,
        'splits' => SplitList::class,
        'authorizeOnly' => 'bool',
        'creditCardHolderInfo' => CreditCardHolderInfo::class,
        'creditCard' => \CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments\CreditCard::class,
        'refunds' => RefundList::class,
        'dueDate' => 'date',
        'dateCreated' => 'date'
    ];


    /**
     * @throws AsaasException
     */
    public function getPayments(string $status = null, int $offset = 0, int $limit = 10): ?ListResponse
    {
        return static::getRepository()->getPayments($this->getId(), $status, $offset, $limit);
    }

    /**
     * @throws AsaasException
     */
    public function getPaymentBook(string $sort = null, string $order = null): ?string
    {
        return static::getRepository()->getPaymentBook($this->getId(), $sort, $order);
    }

    public function getRefundsList(): ?RefundList
    {
        return $this->getRefunds();
    }

    /**
     * @throws AsaasException
     */
    public function updateSplits(SplitList $splits): ?SplitList
    {
        return static::getRepository()->updateSplits($this->getId(), $splits);
    }

    /**
     * @throws AsaasException|GuzzleException
     */
    public function refund(): ?self
    {
        return static::getRepository()->refund($this->getId());
    }
}
