<?php

namespace CaiqueMcz\AsaasPaymentGateway\Model;

use CaiqueMcz\AsaasPaymentGateway\Exception\AsaasException;
use CaiqueMcz\AsaasPaymentGateway\Repository\SplitRepository;
use CaiqueMcz\AsaasPaymentGateway\Response\ListResponse;

/**
 * Split Model
 *
 * @method string|null getId() Identificador único do split pago no Asaas
 * @method self setId(string|null $id)
 * @method string getWalletId() Identificador da carteira Asaas que será transferido
 * @method self setWalletId(string $walletId)
 * @method float|null getFixedValue() Valor fixo a ser transferido para a conta quando a cobrança for recebida
 * @method self setFixedValue(?float $fixedValue)
 * @method float|null getPercentualValue() Percentual sobre o valor líquido da cobrança a ser transferido
 * quando for recebida
 * @method self setPercentualValue(?float $percentualValue)
 * @method float|null getTotalValue() Valor total do split que será enviado
 * @method self setTotalValue(?float $totalValue)
 * @method string|null getCancellationReason() Motivo de cancelamento do split
 * @method self setCancellationReason(?string $cancellationReason)
 * @method string|null getStatus() SplitStatus do split
 * @method self setStatus(?string $status)
 * @method string|null getExternalReference() Identificador do split no seu sistema
 * @method self setExternalReference(?string $externalReference)
 * @method string|null getDescription() Descrição do split
 * @method self setDescription(?string $description)
 * @method bool  delete()
 * @method static SplitRepository getRepository()
 * @method static self fromArray(array $data)
 * @method static self getById(string $id)
 * @method self|null refresh()
 */
class Split extends AbstractModel
{
    protected array $fields = [
        'id',
        'walletId',
        'fixedValue',
        'percentualValue',
        'totalValue',
        'cancellationReason',
        'status',
        'externalReference',
        'description'
    ];

    protected array $requiredFields = [
        'walletId'
    ];

    protected array $casts = [
        'fixedValue' => 'float',
        'percentualValue' => 'float',
        'totalValue' => 'float'
    ];


    /**
     * @throws AsaasException
     */
    public static function getPaid(string $id): ?Split
    {
        return static::getRepository()->getPaid($id);
    }

    /**
     * @throws AsaasException
     */
    public static function getReceived(string $id): ?Split
    {
        return static::getRepository()->getReceived($id);
    }

    /**
     * @throws AsaasException
     */
    public static function getAllPaid(array $filters = []): ?ListResponse
    {
        return static::getRepository()->getAllPaid($filters);
    }

    /**
     * @throws AsaasException
     */
    public static function getAllReceived(array $filters = []): ?ListResponse
    {
        return static::getRepository()->getAllReceived($filters);
    }
}
