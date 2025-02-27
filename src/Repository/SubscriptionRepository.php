<?php

namespace CaiqueMcz\AsaasPaymentGateway\Repository;

use CaiqueMcz\AsaasPaymentGateway\Exception\AsaasException;
use CaiqueMcz\AsaasPaymentGateway\Model\Payment;
use CaiqueMcz\AsaasPaymentGateway\Model\Subscription;
use CaiqueMcz\AsaasPaymentGateway\Response\ListResponse;
use GuzzleHttp\Exception\GuzzleException;

class SubscriptionRepository extends AbstractRepository
{
    /**
     * Cria uma assinatura com cartão de crédito
     *
     * @param array $data Dados da assinatura
     * @return Subscription|null
     * @throws AsaasException
     * @throws GuzzleException
     */
    public function createWithCreditCard(array $data): ?Subscription
    {
        $data = $this->prepareSendData($data);
        $response = $this->client->post($this->endpoint . '/', $data);
        return call_user_func([Subscription::class, 'fromArray'], $response);
    }

    /**
     * Cria uma assinatura com cartão de crédito tokenizado
     *
     * @param array $data Dados da assinatura
     * @return Subscription|null
     * @throws AsaasException
     * @throws GuzzleException
     */
    public function createWithCreditCardTokenized(array $data): ?Subscription
    {
        $data = $this->prepareSendData($data);
        $response = $this->client->post($this->endpoint . '/', $data);
        return call_user_func([Subscription::class, 'fromArray'], $response);
    }

    /**
     * Obtém as cobranças de uma assinatura
     *
     * @param string $id ID da assinatura
     * @param array $filters Filtros opcionais para busca
     * @return ListResponse|null Lista de pagamentos da assinatura
     * @throws AsaasException
     */
    public function getPayments(string $id, array $filters = []): ?ListResponse
    {
        $response = $this->client->get($this->endpoint . '/' . $id . '/payments', $filters);
        $response['modelClass'] = Payment::class;
        return ListResponse::fromArray($response);
    }

    /**
     * Obtém o carnê de pagamento da assinatura
     *
     * @param string $id ID da assinatura
     * @param string|null $sort Ordenação
     * @param string|null $order Direção da ordenação (asc ou desc)
     * @return string|null URL do carnê ou null
     * @throws AsaasException
     */
    public function getPaymentBook(string $id, ?string $sort = null, ?string $order = null): ?string
    {
        $params = [];
        if (!is_null($sort)) {
            $params['sort'] = $sort;
        }
        if (!is_null($order)) {
            $params['order'] = $order;
        }

        $response = $this->client->get($this->endpoint . '/' . $id . '/paymentBook', $params);

        if (isset($response[0]) && is_string($response[0])) {
            return $response[0];
        }

        return null;
    }
}
