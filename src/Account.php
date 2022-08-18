<?php

namespace RazorInformatics\RiNotifierPhp;


use GuzzleHttp\Exception\GuzzleException;

class Account extends Service
{
	/**
	 * Fetch account details.
	 *
	 * @return array
	 */
	public function details(){
		try {
			$response = $this->client->get('balance');
		} catch (GuzzleException $e) {
			return $this->error($e->getCode(),$e->getMessage());
		}
		return $this->success($response);
	}

	/**
	 * Fetch account details.
	 *
	 * @return array
	 */
	public function fetchDetails(){
		return $this->details();
	}

	/**
	 * Fetch account details.
	 *
	 * @return array
	 */
	public function getDetails(){
		return $this->details();
	}
}