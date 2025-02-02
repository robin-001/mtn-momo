<?php

namespace Angstrom\MoMo\Tests;

use Angstrom\MoMo\Disbursements;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class DisbursementsTest extends TestCase
{
    protected Disbursements $disbursements;
    protected MockHandler $mockHandler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->mockHandler);
        $httpClient = new Client(['handler' => $handlerStack]);

        $this->disbursements = new Disbursements();
        $this->disbursements->setHttpClient($httpClient);

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

        $result = $this->disbursements->transfer(
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

        $status = $this->disbursements->getTransferStatus('test-ref-id');

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

        $balance = $this->disbursements->getAccountBalance();

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

        $status = $this->disbursements->checkAccountHolderStatus('256772123456');

        $this->assertTrue($status['result']);
    }

    public function test_transfer_failure()
    {
        $this->expectException(\Exception::class);

        $this->mockHandler->append(
            new Response(400, [], json_encode([
                'message' => 'Invalid amount'
            ]))
        );

        $this->disbursements->transfer(
            -100,
            'EUR',
            'ext-123',
            '256772123456'
        );
    }
}
