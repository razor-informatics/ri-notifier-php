<?php

namespace RazorInformatics\RiNotifierPhp;

use GuzzleHttp\Client;

abstract class Service
{
	protected $client;

	protected $apiKey;

	public function __construct(Client $client, string $apiKey)
	{
		$this->client = $client;
		$this->apiKey = $apiKey;
	}

	/**
	 * @param int $code
	 * @param string $message
	 * @param array $data
	 * @return array
	 */
	protected function error($code, $message = '', $data = [])
	{
		switch ($code) {
			case 0:
				$message = "Application could not reach our servers.";
				break;
			case 401:
				$message = "Unauthenticated - Check your api token";
				break;
			case 500:
				$message = "Server Error - Unhandled error happen on our end";
				break;
			case 503:
				$message = "Changing things up to make things way better, Server Maintenance Underway";
				break;
			case 422:
				$message = "Missing data in the request";
				break;
			default:
				if (is_null($message)) {
					$message = "some error occurred !";
				}
		}
		return [
			'status' => 'error',
			'message' => $message,
			'data' => $data
		];
	}


	/**
	 * @param $data
	 * @return array
	 */
	protected function success($data)
	{
		$data = json_decode($data->getBody()->getContents(), false);
		return [
			'status' => 'success',
			'data' => (isset($data->data))?$data->data:$data
		];
	}
}