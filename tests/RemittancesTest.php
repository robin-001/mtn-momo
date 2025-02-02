<?php

namespace Angstrom\MoMo\Tests;

use Angstrom\MoMo\Remittances;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class RemittancesTest extends TestCase
{
    protected Remittances $remittances;
    protected MockHandler $mockHandler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->mockHandler);
        $httpClient = new Client(['handler' => $handlerStack]);

        $this->remittances = new Remittances();
        $this->remittances->setHttpClient($httpClient);

        // Mock token response
        $this->mockHandler->append(
            new Response(200, [], json_encode(['access_token' => 'test-token']))
        );
    }

    public function test_transfer()
    {
        $this->mockHandler->append(
            new Response(202, ['X-Reference-Id' => 'test-ref-id'])
        );

        $result = $this->remittances->transfer(
            100,
            'EUR',
            'ext-123',
            '256772123456',
            'MSISDN',
            'Test transfer',
            'Test note'
        );

        $this->assertEquals(202, $result['status']);
        $this->assertNotEmpty($result['referenceId']);
    }

    public function test_get_transfer_status()
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

        $status = $this->remittances->getTransferStatus('test-ref-id');

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

        $balance = $this->remittances->getAccountBalance();

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

        $status = $this->remittances->checkAccountHolderStatus('256772123456');

        $this->assertTrue($status['result']);
    }

    public function test_get_exchange_rate()
    {
        $this->mockHandler->append(
            new Response(200, [], json_encode([
                'sourceCurrency' => 'EUR',
                'targetCurrency' => 'UGX',
                'rate' => '4000.0'
            ]))
        );

        $rate = $this->remittances->getExchangeRate('EUR', 'UGX');

        $this->assertEquals('EUR', $rate['sourceCurrency']);
        $this->assertEquals('UGX', $rate['targetCurrency']);
        $this->assertEquals('4000.0', $rate['rate']);
    }

    public function test_transfer_failure()
    {
        $this->expectException(\Exception::class);

        $this->mockHandler->append(
            new Response(400, [], json_encode([
                'message' => 'Invalid amount'
            ]))
        );

        $this->remittances->transfer(
            -100,
            'EUR',
            'ext-123',
            '256772123456'
        );
    }

    public function test_get_exchange_rate_failure()
    {
        $this->expectException(\Exception::class);

        $this->mockHandler->append(
            new Response(400, [], json_encode([
                'message' => 'Invalid currency pair'
            ]))
        );

        $this->remittances->getExchangeRate('INVALID', 'UGX');
    }
}
