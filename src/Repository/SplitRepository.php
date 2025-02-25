<?php

namespace AsaasPaymentGateway\Repository;

use AsaasPaymentGateway\Exception\AsaasException;
use AsaasPaymentGateway\Model\AbstractModel;
use AsaasPaymentGateway\Model\Split;
use AsaasPaymentGateway\Response\ListResponse;

class SplitRepository extends AbstractRepository
{
    public function __construct(string $modelClass, ?string $endpoint = null)
    {
        parent::__construct($modelClass, "payments/splits");
    }

    /**
     * @throws AsaasException
     */
    public function getPaid(string $id): ?Split
    {
        $response = $this->client->get($this->endpoint . '/paid/' . $id);
        return Split::fromArray($response);
    }

    /**
     * @throws AsaasException
     */
    public function getReceived(string $id): ?Split
    {
        $response = $this->client->get($this->endpoint . '/received/' . $id);
        return Split::fromArray($response);
    }


    /**
     * @throws AsaasException
     */
    public function getAllPaid($filters = []): ?ListResponse
    {
        $response = $this->client->get($this->endpoint . '/paid', $filters);
        $response['modelClass'] = $this->modelClass;
        return ListResponse::fromArray($response);
    }

    /**
     * @throws AsaasException
     */
    public function getAllReceived($filters = []): ?ListResponse
    {
        $response = $this->client->get($this->endpoint . '/received', $filters);
        $response['modelClass'] = $this->modelClass;
        return ListResponse::fromArray($response);
    }
}
