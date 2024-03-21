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

	/**
	 * @return Message
	 */
	public function message()
	{
		return new Message($this->client, $this->apiKey);
	}

	/**
	 * @return Account
	 */
	public function account()
	{
		return new Account($this->client, $this->apiKey);
	}

    /**
     * @return Gateway
     */
    public function gateway($gateway = Constants::GATEWAY_NOTIFIER)
    {
        return new Gateway($this->client, $this->apiKey, $gateway);
    }

}