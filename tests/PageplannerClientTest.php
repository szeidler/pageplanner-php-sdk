<?php

namespace szeidler\Pageplanner\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Command\Guzzle\Description;
use szeidler\Pageplanner\PageplannerClient;

/**
 * Tests the PageplannerClient class.
 *
 * @package szeidler\Pageplanner\Tests
 * @see     \szeidler\Pageplanner\PageplannerClient
 */
class PageplannerClientTest extends PageplannerTestWrapper
{

    protected $client;

    public function setUp()
    {
        parent::setUp();
    }

    /**
     * Tests, that a http client can be injected via the config array.
     */
    public function testCustomClientFromConfig()
    {
        $base_uri = 'http://httpbin.org/';

        // Create a custom client.
        $custom_client = new Client(['base_uri' => $base_uri]);

        // Inject the custom client as configuration into the PageplannerClient.
        $client = new PageplannerClient(
          [
            'client'  => $custom_client,
            'baseUrl' => getenv('BASE_URL'),
          ]
        );

        $this->assertEquals(
          $custom_client,
          $client->getHttpClient(),
          'The PageplannerClient must return the base_uri of the injected Client.'
        );
    }

    /**
     * Test to add custom client configuration.
     */
    public function testCustomClientConfiguration()
    {
        // Create a custom client configuration.
        $timeout = 2.0;
        $proxy = 'socks5://10.254.254.254:8123';
        $client_config = ['timeout' => $timeout, 'proxy' => $proxy];

        // Inject the custom client as configuration into the PageplannerClient.
        $client = new PageplannerClient(
          [
            'baseUrl'          => getenv('BASE_URL'),
            'access_token_url' => getenv('ACCESS_TOKEN_URL'),
            'client_id'        => getenv('CLIENT_ID'),
            'client_secret'    => getenv('CLIENT_SECRET'),
            'client_config'    => $client_config,
          ]
        );

        $this->assertEquals(
          $timeout,
          $client->getHttpClient()->getConfig('timeout'),
          'The PageplannerClient must return the timeout value of the client configuration'
        );
        $this->assertEquals(
          $proxy,
          $client->getHttpClient()->getConfig('proxy'),
          'The PageplannerClient must return the proxy value of the client configuration'
        );
    }

    /**
     * Test, that custom client configuration is an array.
     *
     * @expectedException InvalidArgumentException
     */
    public function testCustomClientConfigurationMustBeAnArray()
    {
        $client_config = 'socks5://10.254.254.254:8123';

        // Inject the custom client as configuration into the PageplannerClient.
        $client = new PageplannerClient(
          [
            'baseUrl'          => getenv('BASE_URL'),
            'access_token_url' => getenv('ACCESS_TOKEN_URL'),
            'client_id'        => getenv('CLIENT_ID'),
            'client_secret'    => getenv('CLIENT_SECRET'),
            'client_config'    => $client_config,
          ]
        );
    }

    /**
     * Tests, that a service description can be injected via the config array.
     */
    public function testCustomDescriptiontFromConfig()
    {
        $description = new Description([
          'baseUri'    => 'http://httpbin.org/',
          'operations' => [
            'testing' => [
              'httpMethod'    => 'GET',
              'uri'           => '/get{?foo}',
              'responseModel' => 'getResponse',
              'parameters'    => [
                'foo' => [
                  'type'     => 'string',
                  'location' => 'uri',
                ],
                'bar' => [
                  'type'     => 'string',
                  'location' => 'query',
                ],
              ],
            ],
          ],
          'models'     => [
            'getResponse' => [
              'type'                 => 'object',
              'additionalProperties' => [
                'location' => 'json',
              ],
            ],
          ],
        ]);

        // Inject the custom description as configuration into the PageplannerClient.
        $client = new PageplannerClient(
          [
            'description'      => $description,
            'baseUrl'          => getenv('BASE_URL'),
            'access_token_url' => getenv('ACCESS_TOKEN_URL'),
            'client_id'        => getenv('CLIENT_ID'),
            'client_secret'    => getenv('CLIENT_SECRET'),
          ]
        );

        // We don't need the oauth middle ware in our mocked tests.
        $client->getHttpClient()->getConfig('handler')->remove('oauth');

        $this->assertEquals(
          $description,
          $client->getDescription(),
          'The description must return the injected description.'
        );

        $this->assertInstanceOf(
          'GuzzleHttp\Command\ResultInterface',
          $client->testing(['foo' => 'bar', 'bar' => 'foo']),
          'The response must be instance of GuzzleHttp\Command\ResultInterface.'
        );
    }

    /**
     * Tests, that a missing baseUrl throws an exception.
     *
     * @expectedException InvalidArgumentException
     */
    public function testMissingBaseUrlInClientConfiguration()
    {
        $client = new PageplannerClient(
          [
            'access_token_url' => getenv('ACCESS_TOKEN_URL'),
            'client_id'        => getenv('CLIENT_ID'),
            'client_secret'    => getenv('CLIENT_SECRET'),
          ]
        );
    }

    /**
     * Tests, that a missing access token url throws an exception.
     *
     * @expectedException InvalidArgumentException
     */
    public function testMissingApiKeyInClientConfiguration()
    {
        $client = new PageplannerClient(
          [
            'baseUrl'       => getenv('BASE_URL'),
            'client_id'     => getenv('CLIENT_ID'),
            'client_secret' => getenv('CLIENT_SECRET'),
          ]
        );
        $client->getHttpClient()->request('/');
    }

    /**
     * Tests, that a missing client id throws an exception.
     *
     * @expectedException InvalidArgumentException
     */
    public function testMissingClientIdInClientConfiguration()
    {
        $client = new PageplannerClient(
          [
            'baseUrl'          => getenv('BASE_URL'),
            'access_token_url' => getenv('ACCESS_TOKEN_URL'),
            'client_secret'    => getenv('CLIENT_SECRET'),
          ]
        );
        $client->getHttpClient()->request('/');
    }

    /**
     * Tests, that a missing client secret throws an exception.
     *
     * @expectedException InvalidArgumentException
     */
    public function testMissingClientSecretClientConfiguration()
    {
        $client = new PageplannerClient(
          [
            'baseUrl'          => getenv('BASE_URL'),
            'access_token_url' => getenv('ACCESS_TOKEN_URL'),
            'client_id'        => getenv('CLIENT_ID'),
          ]
        );
        $client->getHttpClient()->request('/');
    }
}
