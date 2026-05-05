<?php

namespace RazorInformatics\RiNotifierPhp;

use GuzzleHttp\Client;

class Notifier
{
	protected string $url = "https://notifier.razorinformatics.co.ke/api/";
	protected string $apiKey;
	protected Client $client;

	public function __construct(string $apiKey)
	{
		$this->apiKey = $apiKey;

		$this->client = new Client([
			'base_uri' => $this->url,
			'headers' => [
				'Authorization' => 'Bearer ' . $this->apiKey,
				'Content-Type' => 'multipart/form-data',
				'Accept' => 'application/json'
			]
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

	public function gateway($gateway = Constants::GATEWAY_NOTIFIER): Gateway
    {
        return new Gateway($this->client, $this->apiKey, $gateway);
    }

    public function decoder(): DeHash
    {
        return new DeHash($this->client, $this->apiKey);
    }
}