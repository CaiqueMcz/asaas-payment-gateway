<?php

namespace AsaasPaymentGateway\Model;

use AsaasPaymentGateway\Enums\Payments\BillingType;
use AsaasPaymentGateway\Enums\Subscriptions\SubscriptionCycle;
use AsaasPaymentGateway\Enums\Subscriptions\SubscriptionStatus;
use AsaasPaymentGateway\Exception\AsaasException;
use AsaasPaymentGateway\Helpers\CDate;
use AsaasPaymentGateway\Repository\SubscriptionRepository;
use AsaasPaymentGateway\Response\ListResponse;
use AsaasPaymentGateway\Traits\Model\CreateAbleTrait;
use AsaasPaymentGateway\Traits\Model\DeleteAbleTrait;
use AsaasPaymentGateway\Traits\Model\RestoreAbleTrait;
use AsaasPaymentGateway\Traits\Model\UpdateAbleTrait;
use AsaasPaymentGateway\ValueObject\Payments\CreditCard;
use AsaasPaymentGateway\ValueObject\Payments\CreditCardHolderInfo;
use AsaasPaymentGateway\ValueObject\Payments\Discount;
use AsaasPaymentGateway\ValueObject\Payments\Fine;
use AsaasPaymentGateway\ValueObject\Payments\Interest;
use AsaasPaymentGateway\ValueObject\Payments\SplitList;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Subscription Model
 *
 * @method string|null getId() Identificador único da assinatura no Asaas
 * @method self setId(string|null $id)
 * @method CDate|null getDateCreated() Data de criação da assinatura
 * @method self setDateCreated(CDate $dateCreated)
 * @method string getCustomer() Identificador único do cliente
 * @method self setCustomer(string $customer)
 * @method string|null getPaymentLink() Identificador único do link de pagamentos ao qual a assinatura pertence
 * @method self setPaymentLink(string|null $paymentLink)
 * @method BillingType|null getBillingType() Forma de pagamento
 * @method self setBillingType(BillingType|null $billingType)
 * @method SubscriptionCycle|null getCycle() Periodicidade da cobrança
 * @method self setCycle(SubscriptionCycle|null $cycle)
 * @method float getValue() Valor da assinatura
 * @method self setValue(float $value)
 * @method CDate getNextDueDate() Vencimento do próximo pagamento a ser gerado
 * @method self setNextDueDate(CDate $nextDueDate)
 * @method CDate|null getEndDate() Data limite para vencimento das cobranças
 * @method self setEndDate(CDate $endDate)
 * @method string|null getDescription() Descrição da assinatura
 * @method self setDescription(string|null $description)
 * @method SubscriptionStatus|null getStatus() Status da assinatura
 * @method self setStatus(SubscriptionStatus|null $status)
 * @method Discount|null getDiscount() Informações de desconto
 * @method self setDiscount(Discount|null $discount)
 * @method Fine|null getFine() Informações de multa para pagamento após o vencimento
 * @method self setFine(Fine|null $fine)
 * @method Interest|null getInterest() Informações de juros para pagamento após o vencimento
 * @method self setInterest(Interest|null $interest)
 * @method bool|null isDeleted() Informa se a assinatura foi removida
 * @method self setIsDeleted(bool|null $deleted)
 * @method int|null getMaxPayments() Número máximo de cobranças a serem geradas para esta assinatura
 * @method self setMaxPayments(int|null $maxPayments)
 * @method string|null getExternalReference() Identificador da assinatura no seu sistema
 * @method self setExternalReference(string|null $externalReference)
 * @method SplitList|null getSplits() Informações de split
 * @method self setSplits(SplitList|null $splits)
 * @method array|null getCallback() Informações de redirecionamento automático após pagamento
 * @method self setCallback(array|null $callback)
 * @method CreditCard|null getCreditCard() Informações do cartão de crédito
 * @method self setCreditCard(CreditCard|null $creditCard)
 * @method CreditCardHolderInfo|null getCreditCardHolderInfo() Informações do titular do cartão de crédito
 * @method self setCreditCardHolderInfo(CreditCardHolderInfo|null $creditCardHolderInfo)
 * @method string|null getCreditCardToken() Token do cartão de crédito para uso da funcionalidade de tokenização
 * @method self setCreditCardToken(string|null $creditCardToken)
 * @method string|null getRemoteIp() IP de onde o cliente está fazendo a compra
 * @method self setRemoteIp(string|null $remoteIp)
 * @method static self create(array $data)
 * @method self update(array $data)
 * @method self restore()
 * @method bool delete()
 * @method static SubscriptionRepository getRepository()
 * @method static self fromArray(array $data)
 * @method static self getById(string $id)
 * @method self|null refresh()
 * @method self|null save()
 */
class Subscription extends AbstractModel
{
    use CreateAbleTrait;
    use UpdateAbleTrait;
    use RestoreAbleTrait;
    use DeleteAbleTrait;

    protected array $fields = [
        'id',
        'dateCreated',
        'customer',
        'paymentLink',
        'billingType',
        'cycle',
        'value',
        'nextDueDate',
        'endDate',
        'description',
        'status',
        'discount',
        'fine',
        'interest',
        'deleted',
        'maxPayments',
        'externalReference',
        'splits',
        'callback',
        'creditCard',
        'creditCardHolderInfo',
        'creditCardToken',
        'remoteIp'
    ];

    protected array $requiredFields = [
        'customer',
        'billingType',
        'value',
        'nextDueDate',
        'cycle'
    ];

    protected array $casts = [
        'value' => 'float',
        'maxPayments' => 'int',
        'deleted' => 'bool',
        'discount' => Discount::class,
        'interest' => Interest::class,
        'fine' => Fine::class,
        'splits' => SplitList::class,
        'creditCard' => CreditCard::class,
        'creditCardHolderInfo' => CreditCardHolderInfo::class,
        'billingType' => BillingType::class,
        'cycle' => SubscriptionCycle::class,
        'status' => SubscriptionStatus::class,
        'nextDueDate' => 'date',
        'endDate' => 'date',
        'dateCreated' => 'date',
    ];

    /**
     * @throws AsaasException
     * @throws GuzzleException
     */
    public static function createWithCreditCard(array $data): ?self
    {
        $extraFieldsRequired = ['remoteIp', 'creditCard', 'creditCardHolderInfo'];
        foreach ($extraFieldsRequired as $field) {
            if (empty($data[$field])) {
                throw AsaasException::requiredFieldException($field);
            }
        }

        return static::getRepository()->createWithCreditCard($data);
    }

    /**
     * @throws AsaasException
     * @throws GuzzleException
     */
    public static function createWithCreditCardTokenized(array $data): ?self
    {
        $extraFieldsRequired = ['remoteIp', 'creditCardToken'];
        foreach ($extraFieldsRequired as $field) {
            if (empty($data[$field])) {
                throw AsaasException::requiredFieldException($field);
            }
        }

        return static::getRepository()->createWithCreditCardTokenized($data);
    }

    /**
     * Obtém as cobranças de uma assinatura
     *
     * @param array $filters Filtros opcionais para busca
     * @return ListResponse|null Lista de pagamentos da assinatura
     * @throws AsaasException
     */
    public function getPayments(array $filters = []): ?ListResponse
    {
        $this->hasIdOrFails();
        return self::getRepository()->getPayments($this->getId(), $filters);
    }

    /**
     * Obtém o carnê de pagamento da assinatura
     *
     * @param string|null $sort Ordenação
     * @param string|null $order Direção da ordenação (asc ou desc)
     * @return string|null URL do carnê ou null
     * @throws AsaasException
     */
    public function getPaymentBook(?string $sort = null, ?string $order = null): ?string
    {
        $this->hasIdOrFails();
        return self::getRepository()->getPaymentBook($this->getId(), $sort, $order);
    }
}
