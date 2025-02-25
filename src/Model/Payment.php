<?php

namespace AsaasPaymentGateway\Model;

use AsaasPaymentGateway\Enums\Payments\BillingType;
use AsaasPaymentGateway\Enums\Payments\PaymentStatus;
use AsaasPaymentGateway\Exception\AsaasException;
use AsaasPaymentGateway\Helpers\CDate;
use AsaasPaymentGateway\Repository\PaymentRepository;
use AsaasPaymentGateway\Response\BillingInfoResponse;
use AsaasPaymentGateway\Response\ListResponse;
use AsaasPaymentGateway\Traits\Model\CreateAbleTrait;
use AsaasPaymentGateway\Traits\Model\DeleteAbleTrait;
use AsaasPaymentGateway\Traits\Model\RestoreAbleTrait;
use AsaasPaymentGateway\Traits\Model\UpdateAbleTrait;
use AsaasPaymentGateway\ValueObject\Payments\Chargeback;
use AsaasPaymentGateway\ValueObject\Payments\CreditCard;
use AsaasPaymentGateway\ValueObject\Payments\CreditCardHolderInfo;
use AsaasPaymentGateway\ValueObject\Payments\Discount;
use AsaasPaymentGateway\ValueObject\Payments\Fine;
use AsaasPaymentGateway\ValueObject\Payments\Installment;
use AsaasPaymentGateway\ValueObject\Payments\Interest;
use AsaasPaymentGateway\ValueObject\Payments\Limits\PaymentLimitsResponse;
use AsaasPaymentGateway\ValueObject\Payments\PaymentDocument;
use AsaasPaymentGateway\ValueObject\Payments\PixQrcode;
use AsaasPaymentGateway\ValueObject\Payments\Refund;
use AsaasPaymentGateway\ValueObject\Payments\SplitList;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Payment Model
 *
 * @method string|null getId() Identificador único da cobrança no Asaas
 * @method self  setId(string|null $id)
 * @method CDate|null  getDateCreated() Data de criação
 * @method self setDateCreated(CDate $dateCreated)
 * @method string getCustomer() Identificador único do cliente ao qual a cobrança pertence
 * @method self setCustomer(string $customer)
 * @method string|null getSubscription() Identificador único da assinatura (quando cobrança recorrente)
 * @method self setSubscription(string|null $subscription)
 * @method string|null getInstallment() Identificador único do parcelamento (quando cobrança parcelada)
 * @method self setInstallment(string|null $installment)
 * @method string|null getPaymentLink() Identificador único do link de pagamentos ao qual a cobrança pertence
 * @method self setPaymentLink(string|null $paymentLink)
 * @method float getValue() Valor da cobrança
 * @method self setValue(float $value)
 * @method float|null getNetValue() Valor líquido da cobrança após desconto da tarifa do Asaas
 * @method self setNetValue(float|null $netValue)
 * @method float|null getOriginalValue() Valor original da cobrança (preenchido quando paga com juros e multa)
 * @method self setOriginalValue(float|null $originalValue)
 * @method float|null getInterestValue() Valor calculado de juros e multa que deve ser pago após o vencimento da
 * cobrança
 * @method self setInterestValue(float|null $interestValue)
 * @method string|null getDescription() Descrição da cobrança
 * @method self setDescription(string|null $description)
 * @method BillingType getBillingType() Forma de pagamento
 * PIX)
 * @method self setBillingType(BillingType $billingType)
 * @method PaymentStatus getStatus() Status da cobrança
 * @method self setStatus(PaymentStatus $status)
 * @method CDate|null getDueDate() Data de vencimento da cobrança
 * @method self setDueDate(CDate $dueDate)
 * @method CDate|null getOriginalDueDate() Vencimento original no ato da criação da cobrança
 * @method self setOriginalDueDate(CDate $originalDueDate)
 * @method CDate|null getPaymentDate() Data de liquidação da cobrança no Asaas
 * @method self setPaymentDate(CDate $paymentDate)
 * @method CDate|null getClientPaymentDate() Data em que o cliente efetuou o pagamento do boleto
 * @method self setClientPaymentDate(CDate $clientPaymentDate)
 * @method int|null getInstallmentNumber() Número da parcela
 * @method self setInstallmentNumber(int|null $installmentNumber)
 * @method string|null getInvoiceUrl() URL da fatura
 * @method self setInvoiceUrl(string|null $invoiceUrl)
 * @method string|null getInvoiceNumber() Número da fatura
 * @method self setInvoiceNumber(string|null $invoiceNumber)
 * @method string|null getExternalReference() Campo livre para busca
 * @method self setExternalReference(string|null $externalReference)
 * @method bool|null isDeleted() Determina se a cobrança foi removida
 * @method self setIsDeleted(bool|null $deleted)
 * @method bool|null isAnticipated() Define se a cobrança foi antecipada ou está em processo de antecipação
 * @method self setIsAnticipated(bool|null $anticipated)
 * @method bool|null isAnticipable() Determina se a cobrança é antecipável
 * @method self setIsAnticipable(bool|null $anticipable)
 * @method CDate|null getCreditDate() Indica a data que o crédito ficou disponível
 * @method self setCreditDate(CDate $creditDate)
 * @method CDate|null getEstimatedCreditDate() Data estimada de quando o crédito ficará disponível
 * @method self setEstimatedCreditDate(CDate $estimatedCreditDate)
 * @method string|null getTransactionReceiptUrl() URL do comprovante de confirmação, recebimento, estorno ou remoção
 * @method self setTransactionReceiptUrl(string|null $transactionReceiptUrl)
 * @method string|null getNossoNumero() Identificação única do boleto
 * @method self setNossoNumero(string|null $nossoNumero)
 * @method string|null getBankSlipUrl() URL para download do boleto
 * @method self setBankSlipUrl(string|null $bankSlipUrl)
 * @method Discount|null getDiscount() Informações de desconto
 * @method self setDiscount(Discount|null $discount)
 * @method Fine|null getFine() Informações de multa para pagamento após o vencimento
 * @method self setFine(Fine|null $fine)
 * @method Interest|null getInterest() Informações de juros para pagamento após o vencimento
 * @method self setInterest(Interest|null $interest)
 * @method SplitList|null getSplit() Configurações do split
 * @method self setSplit(SplitList|null $split)
 * @method CreditCard|null getCreditCard() Informações do cartão de crédito
 * @method self setCreditCard(CreditCard|null $creditCard)
 * @method string|null getPixTransaction() Identificador único da transação Pix à qual a cobrança pertence
 * @method self setPixTransaction(string|null $pixTransaction)
 * @method string|null getPixQrCodeId() Identificador único do QrCode estático gerado para determinada chave Pix
 * @method self setPixQrCodeId(string|null $pixQrCodeId)
 * @method bool|null isCanBePaidAfterDueDate() Informa se a cobrança pode ser paga após o vencimento
 * (Somente para boleto)
 * @method self setIsCanBePaidAfterDueDate(bool|null $canBePaidAfterDueDate)
 * @method bool|null isPostalService() Define se a cobrança será enviada via Correios
 * @method self setIsPostalService(bool|null $postalService)
 * @method int|null getDaysAfterDueDateToRegistrationCancellation() Dias após o vencimento para cancelamento do registro
 * (somente para boleto bancário)
 * @method self setDaysAfterDueDateToRegistrationCancellation(int|null $days)
 * @method Chargeback|null getChargeback() Informações de chargeback
 * @method self setChargeback(Chargeback|null $chargeback)
 * @method Refund[]|null getRefunds() Informações de estornos
 * @method self  setRefunds(array|null $refunds)
 * @method string|null getCreditCardToken() Token do cartão de crédito para uso da funcionalidade de tokenização de
 * cartão de crédito. Caso informado, os campos acima não são obrigatórios.
 * @method self setCreditCardToken(string|null $creditCardToken)
 * @method string|null getRemoteIp() IP de onde o cliente está fazendo a compra. Não deve ser informado o IP do seu
 * servidor.
 * @method self setRemoteIp(string|null $remoteIp)
 * @method bool|null isAuthorizeOnly() Realizar apenas a Pré-Autorização da cobrança
 * @method self setIsAuthorizeOnly(bool|null $isAuthorizeOnly)
 * @method static self  create(array $data)
 * @method self  update(array $data)
 * @method self  restore()
 * @method bool  delete()
 * @method static PaymentRepository getRepository()
 * @method static self fromArray(array $data)
 * @method static self getById(string $id)
 * @method self|null refresh()
 * @method self|null save()
 */
class Payment extends AbstractModel
{
    use CreateAbleTrait;
    use UpdateAbleTrait;
    use RestoreAbleTrait;
    use DeleteAbleTrait;

    protected array $fields = [
        'id',
        'dateCreated',
        'customer',
        'subscription',
        'installment',
        'paymentLink',
        'value',
        'netValue',
        'originalValue',
        'interestValue',
        'description',
        'billingType',
        'status',
        'dueDate',
        'originalDueDate',
        'paymentDate',
        'clientPaymentDate',
        'installmentNumber',
        'invoiceUrl',
        'invoiceNumber',
        'externalReference',
        'deleted',
        'anticipated',
        'anticipable',
        'creditDate',
        'estimatedCreditDate',
        'transactionReceiptUrl',
        'nossoNumero',
        'bankSlipUrl',
        'discount',
        'fine',
        'interest',
        'split',
        'creditCard',
        'pixTransaction',
        'pixQrCodeId',
        'canBePaidAfterDueDate',
        'postalService',
        'daysAfterDueDateToRegistrationCancellation',
        'chargeback',
        'refunds',
        'creditCardToken',
        'remoteIp',
        'authorizeOnly'
    ];

    protected array $requiredFields = [
        'customer',
        'billingType',
        'value',
        'dueDate'
    ];

    protected array $casts = [
        'value' => 'float',
        'netValue' => 'float',
        'originalValue' => 'float',
        'interestValue' => 'float',
        'deleted' => 'bool',
        'anticipated' => 'bool',
        'anticipable' => 'bool',
        'canBePaidAfterDueDate' => 'bool',
        'postalService' => 'bool',
        'daysAfterDueDateToRegistrationCancellation' => 'int',
        'installmentNumber' => 'int',
        'discount' => Discount::class,
        'fine' => Fine::class,
        'interest' => Interest::class,
        'split' => SplitList::class,
        'creditCard' => CreditCard::class,
        'chargeback' => Chargeback::class,
        'refunds' => Refund::class,
        'creditCardHolderInfo' => CreditCardHolderInfo::class,
        'authorizeOnly' => 'bool',
        'status' => PaymentStatus::class,
        'billingType' => BillingType::class,
        'dueDate' => 'date',
        'originalDueDate' => 'date',
        'paymentDate' => 'date',
        'clientPaymentDate' => 'date',
        'dateCreated' => 'date',
        'creditDate' => 'date',
        'estimatedCreditDate' => 'date'
    ];


    /**
     * @throws AsaasException
     * @throws GuzzleException
     */
    public static function createWithCreditCardTokenized(array $data): ?Payment
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
     * @throws AsaasException
     * @throws GuzzleException
     */
    public static function createWithCreditCard(array $data): ?Payment
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
     * @throws AsaasException|GuzzleException
     */
    public static function simulate(float $value, ?int $installmentCount, ?array $billingTypes): ?array
    {
        $response = static::getRepository()->simulate($value, $installmentCount, $billingTypes);
        $optionalKeys = ['creditCard', 'bankSlip', 'pix'];
        foreach ($optionalKeys as $key) {
            if (isset($response[$key]['installment'])) {
                $response[$key]['installment'] = Installment::fromArray($response[$key]['installment']);
            }
        }
        return $response;
    }


    /**
     * @throws AsaasException
     */
    public static function getLimits(): ?PaymentLimitsResponse
    {
        return static::getRepository()->getLimits();
    }

    /**
     * @throws AsaasException
     * @throws GuzzleException
     */
    public function captureAuthorizedPayment(): Payment
    {
        return static::getRepository()->captureAuthorizedPayment($this->getId());
    }

    /**
     * @throws AsaasException
     * @throws GuzzleException
     */
    public function payWithCreditCardTokenized(string $creditCardToken): Payment
    {
        $data = ['creditCardToken' => $creditCardToken];
        return static::getRepository()->payWithCreditCard($this->getId(), $data);
    }

    /**
     * @throws AsaasException
     * @throws GuzzleException
     */
    public function payWithCreditCard(CreditCard $creditCard, CreditCardHolderInfo $creditCardHolderInfo): Payment
    {
        $data = ['creditCard' => $creditCard, 'creditCardHolderInfo' => $creditCardHolderInfo];
        return static::getRepository()->payWithCreditCard($this->getId(), $data);
    }

    /**
     * @throws AsaasException
     * @throws GuzzleException
     */
    public function getViewingInfo(): ?array
    {
        return static::getRepository()->getViewingInfo($this->getId());
    }

    /**
     * @throws AsaasException
     * @throws GuzzleException
     */
    public function getIdentificationField(): ?array
    {
        return static::getRepository()->getIdentificationField($this->getId());
    }

    /**
     * @throws AsaasException
     * @throws GuzzleException
     */
    public function refund(?float $value = null, ?string $description = null): Payment
    {
        return static::getRepository()->refund($this->getId(), ['value' => $value, 'description' => $description]);
    }

    /**
     * @throws AsaasException
     * @throws GuzzleException
     */
    public function getPixQrCode(): ?PixQrcode
    {
        return static::getRepository()->getPixQrCode($this->getId());
    }

    /**
     * @throws AsaasException|GuzzleException
     */
    public function confirmReceiveInCash(string $paymentDate, float $value, ?bool $notifyCustomer): ?AbstractModel
    {
        $data = [
            'paymentDate' => $paymentDate,
            'value' => $value,
            'notifyCustomer' => $notifyCustomer
        ];
        return static::getRepository()->confirmReceiveInCash($this->getId(), $data);
    }

    /**
     * @throws AsaasException|GuzzleException
     */
    public function undoConfirmReceiveInCash(): ?AbstractModel
    {
        return static::getRepository()->undoConfirmReceiveInCash($this->getId());
    }

    /**
     * @throws AsaasException|GuzzleException
     */
    public function getBillingInfo(): ?BillingInfoResponse
    {
        $response = static::getRepository()->getBillingInfo($this->getId());

        return BillingInfoResponse::fromArray($response);
    }

    /**
     * @throws \Exception|GuzzleException
     */
    public function uploadDocument(string $documentType, string $fileDir, bool $availableAfterPayment): ?PaymentDocument
    {
        $requestData = [
            'file' => $fileDir,
            'type' => $documentType,
            'availableAfterPayment' => $availableAfterPayment
        ];
        return static::getRepository()->uploadDocument($this->getId(), $requestData);
    }

    /**
     * @throws AsaasException
     */
    public function getDocuments(): ?ListResponse
    {
        return static::getRepository()->getDocuments($this->getId());
    }

    /**
     * @throws AsaasException
     */
    public function updateDocument(
        string $documentId,
        bool $availableAfterPayment,
        string $documentType
    ): ?PaymentDocument {
        $requestData = ['availableAfterPayment' => $availableAfterPayment, 'type' => $documentType];
        return static::getRepository()->updateDocument($this->getId(), $documentId, $requestData);
    }

    /**
     * @throws AsaasException
     */
    public function getDocument(string $documentId): ?PaymentDocument
    {
        return static::getRepository()->getDocument($this->getId(), $documentId);
    }

    /**
     * @throws AsaasException
     * @throws GuzzleException
     */
    public function bankSlipRefund(): bool
    {
        return static::getRepository()->bankSlipRefund($this->getId());
    }

    /**
     * @throws AsaasException
     */
    public function deleteDocument(string $documentId): bool
    {
        return static::getRepository()->deleteDocument($this->getId(), $documentId);
    }
}
