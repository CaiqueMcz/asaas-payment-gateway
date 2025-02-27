<?php

namespace CaiqueMcz\AsaasPaymentGateway\Repository;

use CaiqueMcz\AsaasPaymentGateway\Exception\AsaasException;
use CaiqueMcz\AsaasPaymentGateway\Model\AbstractModel;
use GuzzleHttp\Exception\GuzzleException;
use ReflectionException;

class CreditCardRepository extends AbstractRepository
{
    /**
     * @throws GuzzleException
     * @throws AsaasException
     */
    public function tokenizeCreditCard(array $data): ?AbstractModel
    {
        $response = $this->client->post($this->endpoint . '/tokenizeCreditCard', $data);
        return call_user_func([$this->modelClass, 'fromArray'], $response);
    }

    /**
     * @throws ReflectionException
     */
    protected function getDefaultEndpoint(): string
    {
        return "creditCard";
    }
}
