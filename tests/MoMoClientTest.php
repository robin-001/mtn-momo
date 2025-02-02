<?php

namespace Angstrom\MoMo\Tests;

use Angstrom\MoMo\MoMoClient;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Cache;

class MoMoClientTest extends TestCase
{
    protected MoMoClient $client;
    protected MockHandler $mockHandler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->mockHandler);
        $httpClient = new Client(['handler' => $handlerStack]);

        $this->client = new MoMoClient();
        $this->client->setHttpClient($httpClient);
    }

    public function test_get_token()
    {
        // Clear the cache
        Cache::forget('momo_token');

        // Mock the response
        $this->mockHandler->append(
            new Response(200, [], json_encode(['access_token' => 'test-token']))
        );

        $token = $this->client->getToken();

        $this->assertEquals('test-token', $token);
        $this->assertEquals('test-token', Cache::get('momo_token'));
    }

    public function test_get_token_from_cache()
    {
        // Set token in cache
        Cache::put('momo_token', 'cached-token', 3600);

        $token = $this->client->getToken();

        $this->assertEquals('cached-token', $token);
    }

    public function test_get_token_failure()
    {
        $this->expectException(\Exception::class);

        // Clear the cache
        Cache::forget('momo_token');

        // Mock the error response
        $this->mockHandler->append(
            new Response(401, [], json_encode(['message' => 'Invalid credentials']))
        );

        $this->client->getToken();
    }
}
