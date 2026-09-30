<?php

declare(strict_types=1);

namespace RazorInformatics\RiNotifierPhp\Tests;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use RazorInformatics\RiNotifierPhp\Account;
use RazorInformatics\RiNotifierPhp\Constants;

/**
 * The response handling every service shares, exercised through Account.
 */
class ServiceTest extends TestCase
{
    public function testUnwrapsTheDataKey(): void
    {
        $result = (new Account($this->client($this->json(['data' => ['balance' => 120]]))))->details();

        $this->assertSame(Constants::STATUS_SUCCESS, $result['status']);
        $this->assertEquals((object) ['balance' => 120], $result['data']);
    }

    public function testReturnsTheWholeBodyWithoutADataKey(): void
    {
        $result = (new Account($this->client($this->json(['balance' => 120]))))->details();

        $this->assertSame(Constants::STATUS_SUCCESS, $result['status']);
        $this->assertEquals((object) ['balance' => 120], $result['data']);
    }

    public function testAnEmptyBodyIsASuccessWithEmptyData(): void
    {
        $result = (new Account($this->client(new Response(204))))->details();

        $this->assertSame(['status' => Constants::STATUS_SUCCESS, 'data' => []], $result);
    }

    public function testInvalidJsonIsAnError(): void
    {
        $result = (new Account($this->client(new Response(200, [], '<html>'))))->details();

        $this->assertSame(Constants::STATUS_ERROR, $result['status']);
        $this->assertStringStartsWith('Invalid JSON response from server:', $result['message']);
        $this->assertSame([], $result['data']);
    }

    public function testAConnectionFailureIsAnError(): void
    {
        $client = $this->client(new ConnectException('Could not resolve host', new Request('GET', 'balance')));

        $result = (new Account($client))->details();

        $this->assertSame([
            'status' => Constants::STATUS_ERROR,
            'message' => 'Application could not reach our servers.',
            'data' => [],
        ], $result);
    }

    public function testARequestFailureWithoutAResponseIsAnError(): void
    {
        $client = $this->client(new RequestException('Malformed request', new Request('GET', 'balance')));

        $result = (new Account($client))->details();

        $this->assertSame(['status' => Constants::STATUS_ERROR, 'message' => 'Application could not reach our servers.', 'data' => []], $result);
    }

    public static function serverMessages(): array
    {
        return [
            'payment required' => [402],
            'not found' => [404],
            'validation' => [422],
        ];
    }

    #[DataProvider('serverMessages')]
    public function testKeepsTheServerMessageAndErrors(int $status): void
    {
        $client = $this->client($this->json([
            'message' => 'The phone field is required.',
            'errors' => ['phone' => ['The phone field is required.']],
        ], $status));

        $result = (new Account($client))->details();

        $this->assertSame([
            'status' => Constants::STATUS_ERROR,
            'message' => 'The phone field is required.',
            'data' => ['phone' => ['The phone field is required.']],
        ], $result);
    }

    public function testAServerMessageWithoutErrorsHasEmptyData(): void
    {
        $result = (new Account($this->client($this->json(['message' => 'Contact not found.'], 404))))->details();

        $this->assertSame(['status' => Constants::STATUS_ERROR, 'message' => 'Contact not found.', 'data' => []], $result);
    }

    public static function statusMessages(): array
    {
        return [
            [401, 'Unauthenticated - Check your api token'],
            [404, 'Data requested was not found'],
            [422, 'Missing data in the request'],
            [500, 'Server Error - Unhandled error happen on our end'],
            [503, 'Changing things up to make things way better, Server Maintenance Underway'],
        ];
    }

    #[DataProvider('statusMessages')]
    public function testFallsBackToAMessageForTheStatus(int $status, string $message): void
    {
        $result = (new Account($this->client(new Response($status))))->details();

        $this->assertSame(['status' => Constants::STATUS_ERROR, 'message' => $message, 'data' => []], $result);
    }

    public function testTheServerMessageIsIgnoredForOtherStatuses(): void
    {
        $result = (new Account($this->client($this->json(['message' => 'Unauthenticated.'], 401))))->details();

        $this->assertSame('Unauthenticated - Check your api token', $result['message']);
    }

    public function testAnUnknownStatusUsesTheExceptionMessage(): void
    {
        $result = (new Account($this->client(new Response(429))))->details();

        $this->assertSame(Constants::STATUS_ERROR, $result['status']);
        $this->assertStringContainsString('429', $result['message']);
    }
}
