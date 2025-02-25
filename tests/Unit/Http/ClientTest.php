<?php

namespace AsaasPaymentGateway\Tests\Unit\Http;

use AsaasPaymentGateway\Config\Configuration;
use AsaasPaymentGateway\Exception\AsaasException;
use AsaasPaymentGateway\Exception\AsaasValidationException;
use AsaasPaymentGateway\Gateway;
use AsaasPaymentGateway\Http\Client;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class ClientTest extends TestCase
{
    private Client $client;
    private Configuration $config;
    private MockHandler $mockHandler;
    private string $apiKey = 'test_api_key';
    private string $apiUrl = 'https://sandbox.asaas.com/';

    protected function setUp(): void
    {
        // Initialize Gateway with test configuration
        $this->config = new Configuration($this->apiKey, null, 'sandbox');
        Gateway::init($this->apiKey, null, 'sandbox');

        // Create mock handler for Guzzle
        $this->mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->mockHandler);

        // Create the client
        $this->client = new Client();

        // Inject mocked Guzzle client
        $guzzleClient = new GuzzleClient(['handler' => $handlerStack]);
        $reflection = new ReflectionClass($this->client);
        $property = $reflection->getProperty('client');
        $property->setValue($this->client, $guzzleClient);
    }

    public function testPostRequest(): void
    {
        $expectedResponse = ['success' => true];
        $this->mockHandler->append(
            new Response(200, [], json_encode($expectedResponse))
        );

        $result = $this->client->post('test-endpoint', ['data' => 'test']);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testGetRequest(): void
    {
        $expectedResponse = ['data' => 'test'];
        $this->mockHandler->append(
            new Response(200, [], json_encode($expectedResponse))
        );

        $result = $this->client->get('test-endpoint', ['param' => 'value']);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testPutRequest(): void
    {
        $expectedResponse = ['updated' => true];
        $this->mockHandler->append(
            new Response(200, [], json_encode($expectedResponse))
        );

        $result = $this->client->put('test-endpoint', ['data' => 'test']);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testDeleteRequest(): void
    {
        $expectedResponse = ['deleted' => true];
        $this->mockHandler->append(
            new Response(200, [], json_encode($expectedResponse))
        );

        $result = $this->client->delete('test-endpoint');

        $this->assertEquals($expectedResponse, $result);
    }

    public function testPostWithFile(): void
    {
        // Create a temporary test file
        $tempFile = tempnam(sys_get_temp_dir(), 'test_');
        file_put_contents($tempFile, 'test content');

        $expectedResponse = ['uploaded' => true];
        $this->mockHandler->append(
            new Response(200, [], json_encode($expectedResponse))
        );

        $result = $this->client->postWithFile('test-endpoint', [
            'file' => $tempFile,
            'type' => 'DOCUMENT'
        ]);

        $this->assertEquals($expectedResponse, $result);
        unlink($tempFile);
    }

    public function testValidationErrorResponse(): void
    {
        $errorResponse = [
            'errors' => [
                ['code' => 'ERROR_1', 'description' => 'Validation error']
            ]
        ];

        $this->mockHandler->append(
            new Response(400, [], json_encode($errorResponse))
        );

        $this->expectException(AsaasValidationException::class);
        $this->client->get('test-endpoint');
    }

    public function testRequestException(): void
    {
        $this->mockHandler->append(
            new RequestException(
                'Error Communicating with Server',
                new Request('GET', 'test'),
                new Response(500, [], json_encode(['message' => 'Server Error']))
            )
        );

        $this->expectException(AsaasException::class);
        $this->expectExceptionMessage('Server Error');
        $this->client->get('test-endpoint');
    }

    public function testRequestExceptionWithoutResponse(): void
    {
        $this->mockHandler->append(
            new RequestException(
                'Network Error',
                new Request('GET', 'test')
            )
        );

        $this->expectException(AsaasException::class);
        $this->expectExceptionMessage('Network Error');
        $this->client->get('test-endpoint');
    }

    public function testEndpointParsing(): void
    {
        $expectedResponse = ['success' => true];
        $this->mockHandler->append(
            new Response(200, [], json_encode($expectedResponse))
        );

        $this->client->get('test-endpoint');
        $lastRequest = $this->mockHandler->getLastRequest();

        $this->assertStringContainsString(
            Configuration::getApiVersion() . '/test-endpoint',
            $lastRequest->getUri()->getPath()
        );
    }

    public function testExtraHeaders(): void
    {
        $expectedResponse = ['success' => true];
        $this->mockHandler->append(
            new Response(200, [], json_encode($expectedResponse))
        );

        $extraHeaders = ['Custom-Header' => 'value'];
        $this->client->send('GET', 'test-endpoint', [], $extraHeaders);

        $lastRequest = $this->mockHandler->getLastRequest();
        $this->assertEquals('value', $lastRequest->getHeader('Custom-Header')[0]);
    }

    public function testMultipleFilesUpload(): void
    {
        // Create temporary test files
        $tempFile1 = tempnam(sys_get_temp_dir(), 'test1_');
        $tempFile2 = tempnam(sys_get_temp_dir(), 'test2_');
        file_put_contents($tempFile1, 'test content 1');
        file_put_contents($tempFile2, 'test content 2');

        $expectedResponse = ['uploaded' => true];
        $this->mockHandler->append(
            new Response(200, [], json_encode($expectedResponse))
        );

        $result = $this->client->postWithFile('test-endpoint', [
            'file1' => $tempFile1,
            'file2' => $tempFile2,
            'metadata' => 'test'
        ]);

        $this->assertEquals($expectedResponse, $result);

        // Cleanup
        unlink($tempFile1);
        unlink($tempFile2);
    }

    protected function tearDown(): void
    {
        // Reset Gateway configuration
        $refClass = new ReflectionClass(Gateway::class);
        $configProp = $refClass->getProperty('configuration');
        $configProp->setAccessible(true);
        $configProp->setValue(null, null);
    }
}
