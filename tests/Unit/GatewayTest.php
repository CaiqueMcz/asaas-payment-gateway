<?php

namespace CaiqueMcz\AsaasPaymentGateway\Tests\Unit;

use CaiqueMcz\AsaasPaymentGateway\Config\Configuration;
use CaiqueMcz\AsaasPaymentGateway\Exception\AsaasException;
use CaiqueMcz\AsaasPaymentGateway\Gateway;
use CaiqueMcz\AsaasPaymentGateway\Http\Client;
use CaiqueMcz\AsaasPaymentGateway\Tests\Traits\GatewayTrait;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class GatewayTest extends TestCase
{
    use GatewayTrait;

    public function testInitSetsConfigurationCorrectly(): void
    {

        $config = Gateway::init($this->getGatewayApiKey(), $this->getWebhookAccessToken(), $this->getApiEnvironment());

        $this->assertInstanceOf(Configuration::class, $config);
        $this->assertEquals($this->getGatewayApiKey(), $config->getApiKey());
        $this->assertEquals($this->getWebhookAccessToken(), $config->getWebhookAccessToken());
        $this->assertEquals($this->getApiEnvironment(), $config->getEnvironment());
    }

    /**
     * @throws AsaasException
     */
    public function testGetConfigReturnsConfigurationAfterInit(): void
    {
        $this->initGateway();
        $config = Gateway::getConfig();

        $this->assertInstanceOf(Configuration::class, $config);
        $this->assertEquals($this->getGatewayApiKey(), $config->getApiKey());
        $this->assertEquals($this->getWebhookAccessToken(), $config->getWebhookAccessToken());
        $this->assertEquals($this->getApiEnvironment(), $config->getEnvironment());
    }


    public function testGetConfigThrowsExceptionIfNotInitialized(): void
    {
        $this->expectException(AsaasException::class);
        $this->expectExceptionMessage('Configuration not initialized. Call Gateway::init() first.');
        Gateway::getConfig();
    }

    /**
     * @throws AsaasException
     */
    public function testGetHttpClientReturnsClientInstance(): void
    {
        $this->initGateway();
        $client = Gateway::getHttpClient();
        $this->assertInstanceOf(Client::class, $client);
    }

    /**
     * @throws AsaasException
     */
    public function testGetHttpClientReturnsSingletonInstance(): void
    {
        $this->initGateway();
        $client1 = Gateway::getHttpClient();
        $client2 = Gateway::getHttpClient();
        $this->assertSame($client1, $client2);
    }

    protected function tearDown(): void
    {
        // Reset the static properties of Gateway using reflection
        $refClass = new ReflectionClass(Gateway::class);

        $configProp = $refClass->getProperty('configuration');
        $configProp->setAccessible(true);
        // For static properties, pass null as the object instance.
        $configProp->setValue(null, null);

        $clientProp = $refClass->getProperty('httpClient');
        $clientProp->setAccessible(true);
        $clientProp->setValue(null, null);
    }
}
