<?php

namespace AsaasPaymentGateway\Config;

/**
 * Configuration class for the Asaas Payment Gateway
 *
 * This class handles all configuration settings required for interacting
 * with the Asaas Payment API, including API keys, webhook tokens, and
 * environment settings.
 */
class Configuration
{
    /**
     * The API key used for authentication with Asaas API
     *
     * @var string
     */
    private string $apiKey;

    /**
     * Token used for webhook authentication
     * Can be null if webhooks are not being used
     *
     * @var string|null
     */
    private ?string $webhookAccessToken;

    /**
     * The environment to be used (production or sandbox)
     *
     * @var string
     */
    private string $environment;

    /**
     * Initialize a new Configuration instance
     *
     * @param string      $apiKey             The API key for authentication
     * @param string|null $webhookAccessToken Optional webhook access token
     * @param string      $environment        Environment setting (defaults to 'sandbox')
     */
    public function __construct(string $apiKey, string $webhookAccessToken = null, string $environment = 'sandbox')
    {
        $this->apiKey = $apiKey;
        $this->webhookAccessToken = $webhookAccessToken;
        $this->environment = $environment;
    }

    /**
     * Get the configured API key
     *
     * @return string The API key
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * Get the webhook access token if configured
     *
     * @return string|null The webhook access token or null if not set
     */
    public function getWebhookAccessToken(): ?string
    {
        return $this->webhookAccessToken;
    }

    /**
     * Get the current environment setting
     *
     * @return string The environment ('production' or 'sandbox')
     */
    public function getEnvironment(): string
    {
        return $this->environment;
    }

    /**
     * Get the base API URL based on the current environment
     *
     * Returns the production URL if environment is set to 'production',
     * otherwise returns the sandbox URL
     *
     * @return string The complete base URL for API requests
     */
    public function getApiUrl(): string
    {
        return $this->environment === 'production'
            ? 'https://api.asaas.com/'
            : 'https://api-sandbox.asaas.com/';
    }

    /**
     * Get the current API version
     *
     * @return string The API version string
     */
    public static function getApiVersion(): string
    {
        return 'v3';
    }
}
