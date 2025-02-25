<?php

namespace AsaasPaymentGateway\Repository;

use AsaasPaymentGateway\Exception\AsaasException;
use AsaasPaymentGateway\Exception\AsaasValidationException;
use AsaasPaymentGateway\Gateway;
use AsaasPaymentGateway\Http\Client;
use AsaasPaymentGateway\Model\AbstractModel;
use AsaasPaymentGateway\Response\ListResponse;
use AsaasPaymentGateway\ValueObject\ArrayableInterface;
use GuzzleHttp\Exception\GuzzleException;
use ReflectionClass;
use ReflectionException;

abstract class AbstractRepository
{
    public Client $client;
    protected string $modelClass;
    public string $endpoint;

    /**
     * @throws ReflectionException
     * @throws AsaasException
     */
    public function __construct(string $modelClass, ?string $endpoint = null)
    {
        $this->client = Gateway::getHttpClient();
        $this->modelClass = $modelClass;
        $this->endpoint = $endpoint ?: $this->getDefaultEndpoint();
    }

    /**
     * @throws ReflectionException
     */
    protected function getDefaultEndpoint(): string
    {
        $reflection = new ReflectionClass($this->modelClass);
        return lcfirst($reflection->getShortName()) . 's';
    }

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * @throws GuzzleException
     * @throws AsaasException
     */
    public function create(array $data): ?AbstractModel
    {
        $data = $this->prepareSendData($data);
        $response = $this->client->post($this->endpoint, $data);
        return call_user_func([$this->modelClass, 'fromArray'], $response);
    }

    public function prepareSendData(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_object($value)) {
                if ($value instanceof ArrayableInterface) {
                    $data[$key] = $value->toArray();
                }
            } else {
                $data[$key] = $value;
            }
        }
        return $data;
    }

    /**
     * @throws GuzzleException
     * @throws AsaasException
     */
    public function restore(string $id): ?AbstractModel
    {
        $response = $this->client->post($this->endpoint . '/' . $id . '/restore', []);
        return call_user_func([$this->modelClass, 'fromArray'], $response);
    }

    /**
     * @throws AsaasException
     * @throws GuzzleException
     * @throws AsaasValidationException
     */
    public function delete(string $id): bool
    {
        $response = $this->client->delete($this->endpoint . '/' . $id);
        return isset($response['deleted']) && $response['deleted'] === true;
    }

    /**
     * @throws GuzzleException
     * @throws AsaasException
     */
    public function update(string $id, array $data): ?AbstractModel
    {
        $data = $this->prepareSendData($data);
        $response = $this->client->put($this->endpoint . '/' . $id, $data);
        return call_user_func([$this->modelClass, 'fromArray'], $response);
    }

    /**
     * @throws AsaasException
     */
    public function getById(string $id): ?AbstractModel
    {
        $response = $this->client->get($this->endpoint . '/' . $id);
        return call_user_func([$this->modelClass, 'fromArray'], $response);
    }

    /**
     * @throws AsaasException
     */
    public function get(): AbstractModel
    {
        $response = $this->client->get($this->endpoint);
        return call_user_func([$this->modelClass, 'fromArray'], $response);
    }

    /**
     * @throws AsaasException
     */
    public function list(array $filters = []): ListResponse
    {
        $response = $this->client->get($this->endpoint, $filters);
        $response['modelClass'] = $this->modelClass;
        return ListResponse::fromArray($response);
    }
}
