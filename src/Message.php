<?php

namespace RazorInformatics\RiNotifierPhp;

use GuzzleHttp\Exception\GuzzleException;

class Message extends Service
{
	public function send()
	{

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
			$response = $this->client->get('message/'.$message_id);
		} catch (GuzzleException $e) {
			return $this->error($e->getCode(),$e->getMessage());
		}
		return $this->success($response);
	}

	public function messageDetails($message_id)
	{
		return $this->getMessage($message_id);
	}

	public function fetchMessage($message_id)
	{
		return $this->getMessage($message_id);
	}
}