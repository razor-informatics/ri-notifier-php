<?php

declare(strict_types=1);

namespace RazorInformatics\RiNotifierPhp;

use GuzzleHttp\Client;

class Notifier
{
    protected string $url = 'https://notifier.razorinformatics.co.ke/api/';
    protected readonly Client $client;

    public function __construct(#[\SensitiveParameter] protected readonly string $apiKey)
    {
        $this->client = new Client([
            'base_uri' => $this->url,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
            ],
        ]);
    }

    public function message(): Message
    {
        return new Message($this->client, $this->apiKey);
    }

    public function account(): Account
    {
        return new Account($this->client, $this->apiKey);
    }

    public function gateway(string $gateway = Constants::GATEWAY_NOTIFIER): Gateway
    {
        return new Gateway($this->client, $this->apiKey, $gateway);
    }

    public function contacts(): Contact
    {
        return new Contact($this->client, $this->apiKey);
    }

    public function labels(): Label
    {
        return new Label($this->client, $this->apiKey);
    }

    public function decoder(): DeHash
    {
        return new DeHash($this->client, $this->apiKey);
    }
}
