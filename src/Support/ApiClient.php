<?php

namespace SMSkin\ServiceBus\Support;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;

class ApiClient
{
    private function getClient(): Client
    {
        return app(Client::class);
    }

    /**
     * @param string $uri
     * @param array $body
     * @param array $headers
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function post(string $uri, array $body = [], array $headers = []): ResponseInterface
    {
        return $this->getClient()->request(
            'POST',
            $uri,
            [
                RequestOptions::JSON => $body,
                'headers' => array_merge(
                    [
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json'
                    ],
                    $headers
                )
            ]
        );
    }
}
