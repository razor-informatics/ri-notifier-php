<?php

namespace RazorInformatics\RiNotifierPhp;

use GuzzleHttp\Client;

abstract class Service
{
	protected $client;

	protected $apiKey;

    /**
     * @param Client $client
     * @param string $apiKey
     */
    public function __construct(Client $client, string $apiKey = '')
	{
		$this->client = $client;

        if (is_string($apiKey)) {
            $this->apiKey = $apiKey;
        } else {
            $this->apiKey = '';
        }

	}

	protected function error(int $code, string $message = '', array $data = []): array
    {
		switch ($code) {
			case 0:
				$message = "Application could not reach our servers.";
				break;
			case 401:
				$message = "Unauthenticated - Check your api token";
				break;
			case 404:
				$message = "Data requested was not found";
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
			'status' => Constants::STATUS_ERROR,
			'message' => $message,
			'data' => $data
		];
	}


	protected function success($data): array
    {
        try {
            $data = json_decode($data->getBody()->getContents(), false, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            return $this->error(500, 'Invalid JSON response from server: ' . $e->getMessage());
        }
        return [
			'status' => Constants::STATUS_SUCCESS,
			'data' => $data->data ?? $data
		];
	}
}