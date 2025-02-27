<?php

namespace CaiqueMcz\AsaasPaymentGateway;

use CaiqueMcz\AsaasPaymentGateway\Config\Configuration;
use CaiqueMcz\AsaasPaymentGateway\Exception\AsaasException;
use CaiqueMcz\AsaasPaymentGateway\Http\Client;

class Gateway
{
    private static ?Configuration $configuration = null;
    public static ?Client $httpClient = null;
    public static $interceptors;

    public static function init(
        string $apiKey,
        string $webhookAccessToken = null,
        string $environment = 'sandbox'
    ): Configuration {
        self::$configuration = new Configuration($apiKey, $webhookAccessToken, $environment);
        return self::$configuration;
    }
    public static function addInterceptor(string $method, string $endpoint, array $response)
    {
        $interceptKey = strtolower($method) . ':' . $endpoint;
        self::$interceptors[$interceptKey] = $response;
    }
    /**
     * @throws AsaasException
     */
    public static function getHttpClient(): Client
    {
        if (is_null(self::$httpClient)) {
            self::$httpClient = new Client();
        }
        return self::$httpClient;
    }

    /**
     * @throws AsaasException
     */
    public static function getConfig(): Configuration
    {
        if (is_null(self::$configuration)) {
            throw new AsaasException('Configuration not initialized. Call Gateway::init() first.');
        }
        return self::$configuration;
    }
}
