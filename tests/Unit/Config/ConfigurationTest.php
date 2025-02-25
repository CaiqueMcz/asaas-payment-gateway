<?php

namespace AsaasPaymentGateway\Tests\Unit\Config;

use AsaasPaymentGateway\Config\Configuration;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    public function testGettersReturnCorrectValues(): void
    {
        $apiKey = 'test_api_key';
        $webhookAccessToken = 'test_webhook_token';
        $environment = 'sandbox';
        $config = new Configuration($apiKey, $webhookAccessToken, $environment);
        $this->assertEquals($apiKey, $config->getApiKey());
        $this->assertEquals($webhookAccessToken, $config->getWebhookAccessToken());
        $this->assertEquals($environment, $config->getEnvironment());
    }

    public function testGetApiUrlForSandbox(): void
    {
        $config = new Configuration('key', 'token', 'sandbox');
        $this->assertEquals('https://api-sandbox.asaas.com/', $config->getApiUrl());
    }

    public function testGetApiUrlForProduction(): void
    {
        $config = new Configuration('key', 'token', 'production');
        $this->assertEquals('https://api.asaas.com/', $config->getApiUrl());
    }

    public function testDefaultEnvironmentIsSandbox(): void
    {
        $config = new Configuration('key', 'token');
        $this->assertEquals('sandbox', $config->getEnvironment());
        $this->assertEquals('https://api-sandbox.asaas.com/', $config->getApiUrl());
    }

    public function testGetWebhookAccessTokenReturnsNullIfNotProvided(): void
    {
        $config = new Configuration('key');
        $this->assertNull($config->getWebhookAccessToken());
    }

    public function testGetApiVersion(): void
    {
        $this->assertEquals('v3', Configuration::getApiVersion());
    }
}
