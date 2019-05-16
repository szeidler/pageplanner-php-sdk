<?php

namespace szeidler\Pageplanner\Tests;

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
            'baseUrl'  => getenv('BASE_URL'),
            'access_token_url' => getenv('ACCESS_TOKEN_URL'),
            'client_id' => getenv('CLIENT_ID'),
            'client_secret' => getenv('CLIENT_SECRET'),
            ]
        );
    }
}
