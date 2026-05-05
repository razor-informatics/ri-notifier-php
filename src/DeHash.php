<?php

namespace RazorInformatics\RiNotifierPhp;

use GuzzleHttp\Exception\GuzzleException;

class DeHash extends Service
{
    public function send(string $hash): array
    {
        if (empty($hash)) {
            return $this->error(7, 'hash must be defined.');
        }
        try {
            $response = $this->client->post('hash/lookup', [
                'form_params' => [
                    'hash' => $hash,
                ],
                'timeout' => 10
            ]);
        } catch (GuzzleException $e) {
            return $this->error($e->getCode(), $e->getMessage());
        }
        return $this->success($response);
    }

    public function decode(string $hash): array
    {
        return $this->send($hash);
    }
}