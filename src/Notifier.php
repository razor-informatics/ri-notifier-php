<?php

namespace RazorInformatics\RiNotifierPhp;

use GuzzleHttp\Client;

class Notifier
{
	protected const BASE_DOMAIN = "https://notifier.razorinformatics.co.ke/api/";
	protected $apiKey;
	protected $client;

	public function __construct($apiKey)
	{
		$this->apiKey = $apiKey;

		$this->client = new Client([
			'base_uri' =>  self::BASE_DOMAIN,
			'headers' => [
				'Authorization' => 'Bearer '.$this->apiKey,
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

}