<?php

namespace szeidler\Pageplanner\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use PHPUnit\Framework\TestCase;
use szeidler\Pageplanner\PageplannerClient;

class PageplannerTestWrapper extends TestCase
{

    protected $client;

    public function setUp()
    {
        parent::setUp();

        $this->client = new PageplannerClient(
          [
            'baseUrl'          => getenv('BASE_URL'),
            'access_token_url' => getenv('ACCESS_TOKEN_URL'),
            'client_id'        => getenv('CLIENT_ID'),
            'client_secret'    => getenv('CLIENT_SECRET'),
          ]
        );
    }

    protected function getMockedPageplannerClient(array $responses, MockHandler $mockHandler = null)
    {
        $mockHandler = $mockHandler ?: new MockHandler();
        foreach ($responses as $response) {
            $mockHandler->append($response);
        }

        $config = ['client' => new Client(['handler' => $mockHandler]), 'baseUrl' => 'http://httpbin.org/'];

        return new PageplannerClient($config);
    }
}
