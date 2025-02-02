<?php

namespace Angstrom\MoMo\Tests;

use Angstrom\MoMo\Collections;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class CollectionsTest extends TestCase
{
    protected Collections $collections;
    protected MockHandler $mockHandler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->mockHandler);
        $httpClient = new Client(['handler' => $handlerStack]);

        $this->collections = new Collections();
        $this->collections->setHttpClient($httpClient);

        // Mock token response
        $this->mockHandler->append(
            new Response(200, [], json_encode(['access_token' => 'test-token']))
        );
    }

    public function test_request_to_pay()
    {
        $this->mockHandler->append(
            new Response(202, ['X-Reference-Id' => 'test-ref-id'])
        );

        $result = $this->collections->requestToPay(
            100,
            'EUR',
            'ext-123',
            '256772123456',
            'MSISDN',
            'Test payment',
            'Test note'
        );

        $this->assertEquals(202, $result['status']);
        $this->assertNotEmpty($result['referenceId']);
    }

    public function test_get_transaction_status()
    {
        $this->mockHandler->append(
            new Response(200, [], json_encode([
                'amount' => '100',
                'currency' => 'EUR',
                'financialTransactionId' => '123456789',
                'externalId' => 'ext-123',
                'status' => 'SUCCESSFUL'
            ]))
        );

        $status = $this->collections->getTransactionStatus('test-ref-id');

        $this->assertEquals('SUCCESSFUL', $status['status']);
        $this->assertEquals('100', $status['amount']);
        $this->assertEquals('EUR', $status['currency']);
    }

    public function test_get_account_balance()
    {
        $this->mockHandler->append(
            new Response(200, [], json_encode([
                'availableBalance' => '1000',
                'currency' => 'EUR'
            ]))
        );

        $balance = $this->collections->getAccountBalance();

        $this->assertEquals('1000', $balance['availableBalance']);
        $this->assertEquals('EUR', $balance['currency']);
    }

    public function test_check_account_holder_status()
    {
        $this->mockHandler->append(
            new Response(200, [], json_encode([
                'result' => true
            ]))
        );

        $status = $this->collections->checkAccountHolderStatus('256772123456');

        $this->assertTrue($status['result']);
    }

    public function test_request_to_pay_failure()
    {
        $this->expectException(\Exception::class);

        $this->mockHandler->append(
            new Response(400, [], json_encode([
                'message' => 'Invalid amount'
            ]))
        );

        $this->collections->requestToPay(
            -100,
            'EUR',
            'ext-123',
            '256772123456'
        );
    }
}
