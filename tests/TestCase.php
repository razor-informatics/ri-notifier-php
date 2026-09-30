<?php

declare(strict_types=1);

namespace RazorInformatics\RiNotifierPhp\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Psr\Http\Message\RequestInterface;

abstract class TestCase extends BaseTestCase
{
    /** @var list<array{request: RequestInterface, options: array}> */
    protected array $history = [];

    /**
     * A client that answers with the given responses (or throws the given exceptions) in order,
     * recording every request it makes in $history.
     */
    protected function client(Response|\Throwable ...$responses): Client
    {
        $this->history = [];
        $stack = HandlerStack::create(new MockHandler($responses));
        $stack->push(Middleware::history($this->history));

        return new Client([
            'base_uri' => 'https://notifier.test/api/',
            'handler' => $stack,
        ]);
    }

    protected function json(mixed $body, int $status = 200): Response
    {
        return new Response($status, ['Content-Type' => 'application/json'], json_encode($body, JSON_THROW_ON_ERROR));
    }

    protected function lastRequest(): RequestInterface
    {
        $this->assertNotEmpty($this->history, 'No request was made.');

        return $this->history[array_key_last($this->history)]['request'];
    }

    protected function lastOptions(): array
    {
        return $this->history[array_key_last($this->history)]['options'];
    }

    protected function assertRequest(string $method, string $path, ?string $query = null): void
    {
        $request = $this->lastRequest();
        $this->assertSame($method, $request->getMethod());
        $this->assertSame('/api/' . $path, $request->getUri()->getPath());
        if ($query !== null) {
            $this->assertSame($query, $request->getUri()->getQuery());
        }
    }

    protected function assertNoRequest(): void
    {
        $this->assertSame([], $this->history, 'No request should have been made.');
    }

    protected function jsonBody(): mixed
    {
        return json_decode((string) $this->lastRequest()->getBody(), true, 512, JSON_THROW_ON_ERROR);
    }

    protected function formBody(): array
    {
        parse_str((string) $this->lastRequest()->getBody(), $fields);

        return $fields;
    }
}
