<?php

namespace RazorInformatics\RiNotifierPhp;


use GuzzleHttp\Exception\GuzzleException;

class Gateway extends Service
{
    protected $gateway = '';

    public function __construct($client, $apiKey, string $gateway)
    {
        $this->gateway = $gateway;
        parent::__construct($client, $apiKey);
    }

    /**
     * Fetch account details.
     *
     * @return array
     */
    public function fetchDetails()
    {
        return $this->details();
    }

    /**
     * Fetch account details.
     *
     * @return array
     */
    public function details()
    {
        try {
            $response = $this->client->get('v2/balance', [
                'query' => ['gateway' => $this->gateway]
            ]);
        } catch (GuzzleException $e) {
            return $this->error($e->getCode(), $e->getMessage());
        }
        return $this->success($response);
    }

    /**
     * Fetch account details.
     *
     * @return array
     */
    public function getDetails()
    {
        return $this->details();
    }
}