<?php

namespace RazorInformatics\RiNotifierPhp;

use GuzzleHttp\Client;

class Notifier
{
	protected $url = "https://notifier.razorinformatics.co.ke/api/";
	protected $apiKey;
	protected $client;

	public function __construct($apiKey)
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

}