<?php

namespace CaiqueMcz\AsaasPaymentGateway\Http;

use CaiqueMcz\AsaasPaymentGateway\Config\Configuration;
use CaiqueMcz\AsaasPaymentGateway\Exception\AsaasException;
use CaiqueMcz\AsaasPaymentGateway\Exception\AsaasPageNotFoundException;
use CaiqueMcz\AsaasPaymentGateway\Exception\AsaasValidationException;
use CaiqueMcz\AsaasPaymentGateway\Gateway;
use CaiqueMcz\AsaasPaymentGateway\Helpers\Utils;
use Exception;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

class Client
{
    public static $interceptors = [];
    public GuzzleClient $client;
    private $config;

    /**
     * @throws AsaasException
     */
    public function __construct()
    {
        $config = Gateway::getConfig();
        $this->config = $config;
        $this->client = new GuzzleClient(['base_uri' => $this->config->getApiUrl()]);
    }

    public static function addInterceptor(string $method, string $endpoint, array $response)
    {
        $interceptKey = strtolower($method) . ':' . $endpoint;
        self::$interceptors[$interceptKey] = $response;
    }


    /**
     * @throws GuzzleException
     * @throws AsaasException
     * @throws GuzzleException
     * @throws AsaasException
     * @throws AsaasValidationException
     * @throws AsaasPageNotFoundException
     */
    public function post(string $endpoint, array $data): array
    {
        return $this->send('POST', $endpoint, ['json' => $data]);
    }

    /**
     * @throws GuzzleException
     * @throws AsaasException
     * @throws AsaasValidationException
     * @throws AsaasPageNotFoundException
     */
    public function send(string $method, string $endpoint, array $options = [], array $extraHeaders = []): array
    {
        $interceptKey = strtolower($method) . ':' . $endpoint;
        if (isset(self::$interceptors[$interceptKey])) {
            return self::$interceptors[$interceptKey];
        }
        try {
            $headers = [

                'accept' => 'application/json',
                'access_token' => $this->config->getApiKey()
            ];
            $baseOptions = [

                'headers' => array_merge($headers, $extraHeaders)
            ];

            $options = array_merge($baseOptions, $options);

            $endpoint = $this->parseEndpoint($endpoint);
            $response = $this->client->request($method, $endpoint, $options);
            $contents = $response->getBody()->getContents();
            $json = json_decode($contents, true);
            if ($this->hasErrors($json)) {
                throw new AsaasValidationException($json['errors']);
            }
            if (is_null($json) && !empty($contents)) {
                return [$contents];
            }
            return $json;
        } catch (RequestException $e) {
            $requestResponse = $e->getResponse();
            if (!is_null($requestResponse)) {
                $responseBody = json_decode($requestResponse->getBody(), true);
                if (isset($responseBody['errors'])) {
                    throw new AsaasValidationException($responseBody['errors']);
                }
                if ($e->getCode() === 404) {
                    throw new AsaasPageNotFoundException();
                }
                throw new AsaasException($responseBody['message'] ?? "Unknown error.");
            }
            throw new AsaasException($e->getMessage(), $e->getCode(), $e);
        } catch (AsaasValidationException $e) {
            throw  $e;
        } catch (Exception $e) {
            throw new AsaasException($e->getMessage(), $e->getCode(), $e);
        }
    }

    private function parseEndpoint(string $endpoint): string
    {
        return Configuration::getApiVersion() . "/" . $endpoint;
    }

    private function hasErrors(?array $response): bool
    {
        if (is_null($response)) {
            return false;
        }
        if (isset($response['errors']) && is_array($response['errors']) && count($response['errors']) > 0) {
            return true;
        }
        return false;
    }

    /**
     * @throws GuzzleException
     * @throws AsaasException
     * @throws GuzzleException
     * @throws AsaasException
     * @throws AsaasValidationException
     * @throws AsaasPageNotFoundException
     */
    public function postWithFile(string $endpoint, array $data): array
    {
        $multipart = [];
        foreach ($data as $key => $value) {
            if (file_exists($value)) {
                $multipart[] = $this->generateFileDataUri($value, $key);
            } else {
                $multipart[] = ['name' => $key, 'contents' => $value];
            }
        }
        return $this->send('POST', $endpoint, [
            'multipart' => $multipart
        ]);
    }

    private function generateFileDataUri(string $filePath, string $name): array
    {
        $fileName = basename($filePath);
        return [
            'name' => $name,
            'filename' => $fileName,
            'contents' => fopen($filePath, 'rb'),
        ];
    }

    /**
     * @throws GuzzleException
     * @throws AsaasException
     * @throws GuzzleException
     * @throws AsaasException
     * @throws AsaasValidationException
     * @throws AsaasPageNotFoundException
     */
    public function get(string $endpoint, array $params = []): array
    {
        return $this->send('GET', $endpoint, ['query' => $params]);
    }

    /**
     * @throws GuzzleException
     * @throws AsaasException
     * @throws GuzzleException
     * @throws AsaasException
     * @throws AsaasValidationException
     * @throws AsaasPageNotFoundException
     */
    public function put(string $endpoint, array $data): array
    {
        return $this->send('PUT', $endpoint, ['json' => $data]);
    }

    /**
     * @throws GuzzleException
     * @throws AsaasException
     * @throws GuzzleException
     * @throws AsaasException
     * @throws AsaasValidationException
     * @throws AsaasPageNotFoundException
     */
    public function delete(string $endpoint): array
    {
        return $this->send('DELETE', $endpoint);
    }
}
