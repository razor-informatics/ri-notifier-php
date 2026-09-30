<?php

declare(strict_types=1);

namespace RazorInformatics\RiNotifierPhp;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

abstract class Service
{
    public function __construct(
        protected readonly Client $client,
        protected readonly string $apiKey = '',
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

    protected function success(ResponseInterface $response): array
    {
        try {
            $data = json_decode((string) $response->getBody(), false, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            return $this->error(500, 'Invalid JSON response from server: ' . $e->getMessage());
        }

        return [
            'status' => Constants::STATUS_SUCCESS,
            'data' => $data->data ?? $data,
        ];
    }
}
