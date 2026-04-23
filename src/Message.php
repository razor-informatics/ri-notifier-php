<?php

namespace RazorInformatics\RiNotifierPhp;

use GuzzleHttp\Exception\GuzzleException;

class Message extends Service
{
	/**
	 * @param array $options =['phone_number'=> '0700100100','message'=>'sample message']
	 * @return array
	 */
	public function send(array $options = []): array
    {
		if (!is_array($options) || empty($options['phone_number']) || empty($options['message'])) {
			return $this->error(7, 'phone number and message must be defined.');
		}

		try {
			$response = $this->client->post('message/send', [
				'form_params' => [
					'message' => $options['message'],
					'phone_number' => $options['phone_number'],
                ],
                'timeout' => 10
			]);
		} catch (GuzzleException $e) {
			return $this->error($e->getCode(), $e->getMessage());
		}
		return $this->success($response);
	}

    /**
     * Get message details using a message
     */
	public function getMessage(string $messageId): array
    {
		try {
			$response = $this->client->get('message/' . $messageId);
		} catch (GuzzleException $e) {
			return $this->error($e->getCode(), $e->getMessage());
		}
		return $this->success($response);
	}

	/**
	 * Get message details using a message
	 */
	public function messageDetails(string $messageId): array
    {
		return $this->getMessage($messageId);
	}

	/**
	 * Get message details using a message
	 */
	public function fetchMessage(string $messageId): array
    {
		return $this->getMessage($messageId);
	}
}