<?php

declare(strict_types=1);

namespace RazorInformatics\RiNotifierPhp;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

abstract class Service
{
    public function __construct(
        protected readonly Client $client,
        #[\SensitiveParameter] protected readonly string $apiKey = '',
    ) {
    }

    protected function error(int $code, string $message = '', array $data = []): array
    {
        $message = match ($code) {
            0 => 'Application could not reach our servers.',
            401 => 'Unauthenticated - Check your api token',
            404 => 'Data requested was not found',
            500 => 'Server Error - Unhandled error happen on our end',
            503 => 'Changing things up to make things way better, Server Maintenance Underway',
            422 => 'Missing data in the request',
            default => $message !== '' ? $message : 'some error occurred !',
        };

        return [
            'status' => Constants::STATUS_ERROR,
            'message' => $message,
            'data' => $data,
        ];
    }

    /**
     * Turn a failed request into an error, keeping the server's explanation when it gives one:
     * its `message` for 402, 404 and 422 and the validation `errors` (keyed by field) as the data.
     */
    protected function failed(GuzzleException $e): array
    {
        // Guzzle 7 puts getResponse() on RequestException, Guzzle 8 only on its ResponseException subclass.
        $response = method_exists($e, 'getResponse') ? $e->getResponse() : null;
        if (!$response instanceof ResponseInterface) {
            return $this->error($e->getCode(), $e->getMessage());
        }

        $body = json_decode((string) $response->getBody(), true);
        $body = is_array($body) ? $body : [];
        $code = $response->getStatusCode();

        if (in_array($code, [402, 404, 422], true) && is_string($body['message'] ?? null)) {
            return [
                'status' => Constants::STATUS_ERROR,
                'message' => $body['message'],
                'data' => $body['errors'] ?? [],
            ];
        }

        return $this->error($code, $e->getMessage(), $body['errors'] ?? []);
    }

    /**
     * @param list<string> $keep top level keys of the response to return next to the data, e.g. pagination `links` and `meta`
     */
    protected function success(ResponseInterface $response, array $keep = []): array
    {
        $body = (string) $response->getBody();
        if ($body === '') {
            return [
                'status' => Constants::STATUS_SUCCESS,
                'data' => [],
            ];
        }

        try {
            $data = json_decode($body, false, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            return [
                'status' => Constants::STATUS_ERROR,
                'message' => 'Invalid JSON response from server: ' . $e->getMessage(),
                'data' => [],
            ];
        }

        $result = [
            'status' => Constants::STATUS_SUCCESS,
            'data' => $data->data ?? $data,
        ];
        foreach ($keep as $key) {
            if (isset($data->{$key})) {
                $result[$key] = $data->{$key};
            }
        }

        return $result;
    }
}
