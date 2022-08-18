<?php

namespace RazorInformatics\RiNotifierPhp;

use GuzzleHttp\Exception\GuzzleException;

class Message extends Service
{
	/**
	 * @param $options =['phone_number'=> '0700100100','message'=>'sample message']
	 * @return array
	 */
	public function send($options = [])
	{
		if (!is_array($options) || empty($options['phone_number']) || empty($options['message'])) {
			return $this->error(7, 'phone number and message must be defined.');
		}

		try {
			$response = $this->client->post('message/send', [
				'form_params' => [
					'message' => $options['message'],
					'phone_number' => $options['phone_number'],
				]
			]);
		} catch (GuzzleException $e) {
			return $this->error($e->getCode(), $e->getMessage());
		}
		return $this->success($response);
	}

	/**
	 * Get message details using message if
	 *
	 * @param string $message_id
	 * @return array
	 */
	public function getMessage($message_id)
	{
		try {
			$response = $this->client->get('message/' . $message_id);
		} catch (GuzzleException $e) {
			return $this->error($e->getCode(), $e->getMessage());
		}
		return $this->success($response);
	}

	/**
	 * Get message details using message if
	 *
	 * @param string $message_id
	 * @return array
	 */
	public function messageDetails($message_id)
	{
		return $this->getMessage($message_id);
	}

	/**
	 * Get message details using message if
	 *
	 * @param string $message_id
	 * @return array
	 */
	public function fetchMessage($message_id)
	{
		return $this->getMessage($message_id);
	}
}