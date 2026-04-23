<?php

namespace RazorInformatics\RiNotifierPhp;


use GuzzleHttp\Exception\GuzzleException;

class Account extends Service
{
	/**
	 * Fetch account details.
	 */
	public function details(): array
    {
		try {
            $response = $this->client->get('balance', ['timeout' => 10]);
		} catch (GuzzleException $e) {
			return $this->error($e->getCode(),$e->getMessage());
		}
		return $this->success($response);
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
	public function getDetails(): array
	{
		return $this->details();
	}
}