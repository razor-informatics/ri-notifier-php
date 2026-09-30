<?php

declare(strict_types=1);

namespace RazorInformatics\RiNotifierPhp;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Gateway extends Service
{
    public function __construct(Client $client, #[\SensitiveParameter] string $apiKey, protected readonly string $gateway)
    {
        parent::__construct($client, $apiKey);
    }

    /**
     * Fetch account details.
     */
    public function fetchDetails(): array
    {
        return $this->details();
    }

    /**
     * Fetch account details.
     */
    public function details(): array
    {
        try {
            $response = $this->client->get('v2/balance', [
                'query' => ['gateway' => $this->gateway],
                'timeout' => 10,
            ]);
        } catch (GuzzleException $e) {
            return $this->failed($e);
        }
        return $this->success($response);
    }

    /**
     * Fetch account details.
     */
    public function getDetails(): array
    {
        return $this->details();
    }
}
