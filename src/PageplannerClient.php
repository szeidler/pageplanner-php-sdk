<?php

namespace szeidler\Pageplanner;

use GuzzleHttp\Client;
use GuzzleHttp\Command\Guzzle\Description;
use GuzzleHttp\Command\Guzzle\GuzzleClient;
use GuzzleHttp\HandlerStack;
use kamermans\OAuth2\GrantType\ClientCredentials;
use kamermans\OAuth2\OAuth2Middleware;
use szeidler\Pageplanner\Signer\ClientCredentials\PageplannerSigner;

/**
 * Main Client, that invokes the service description and handles all requests.
 */
class PageplannerClient extends GuzzleClient
{

    /**
     * PageplannerClient constructor.
     *
     * @param array $config
     *   Holds the configuration to initialize the service client.
     */
    public function __construct(array $config = [])
    {
        parent::__construct(
          $this->getClientFromConfig($config),
          $this->getServiceDescriptionFromConfig($config),
          null,
          null,
          null,
          $config
        );
    }

    /**
     * Returns the service client.
     *
     * The service client will be returned based on a injected client object
     * or created with a default configuration.
     *
     * @param array $config
     *   Holds the configuration to initialize the service client.
     *
     * @return \GuzzleHttp\Client
     */
    private function getClientFromConfig(array $config)
    {
        // If a client was provided, return it.
        if (isset($config['client'])) {
            return $config['client'];
        }

        // Ensure, that a baseUrl was provided.
        if (empty($config['baseUrl'])) {
            throw new \InvalidArgumentException('A baseUrl must be provided.');
        }

        // Ensure, that a access_token_url was provided.
        if (empty($config['access_token_url'])) {
            throw new \InvalidArgumentException('An access_token_url for retrieving the access token must be provided.');
        }

        // Ensure, that a access_token_url was provided.
        if (empty($config['client_id'])) {
            throw new \InvalidArgumentException('An client_id for retrieving the access token must be provided.');
        }

        // Ensure, that a client_secret was provided.
        if (empty($config['client_secret'])) {
            throw new \InvalidArgumentException('An client_secret for retrieving the access token must be provided.');
        }

        // Initialize the client handler stack.
        $stack = $this->initializeClientHandlerStack($config);

        // Initialize client config.
        $client_config = [
          'base_uri' => $config['baseUrl'],
          'auth'     => 'oauth',
          'handler'  => $stack,
        ];

        // Apply provided client configuration, if available.
        if (isset($config['client_config'])) {
            // Ensure, the client_config is an array.
            if (!is_array($config['client_config'])) {
                throw new \InvalidArgumentException('A client_config must be an array.');
            }
            $client_config += $config['client_config'];
        }

        // Create a Guzzle client.
        $client = new Client($client_config);

        return $client;
    }

    /**
     * Returns the service description.
     *
     * The service description will be returned based on a injected
     * configuration object or created based on the general service description
     * file.
     *
     * @param array $config
     *    Holds the configuration to initialize the service client.
     *
     * @return \GuzzleHttp\Command\Guzzle\Description
     */
    private function getServiceDescriptionFromConfig(array $config)
    {
        // If a description was provided, return it.
        if (isset($config['description'])) {
            return $config['description'];
        }

        // Ensure, that a baseUrl was provided.
        if (empty($config['baseUrl'])) {
            throw new \InvalidArgumentException('A baseUrl must be provided.');
        }

        // Create new description based of the stored JSON definition.
        $description = new Description(
          ['baseUrl' => $config['baseUrl']]
          + (array)json_decode(
            file_get_contents(__DIR__ . '/../service.json'),
            true
          )
        );

        return $description;
    }

    /**
     * Initializes the basic client handler stack.
     *
     * @param array $config
     *   Holds the configuration to initialize the service client.
     *
     * @return \GuzzleHttp\HandlerStack
     */
    private function initializeClientHandlerStack(array $config)
    {
        $stack = HandlerStack::create();

        // Add oAuth2 middleware.
        $oauth = $this->initializeOAuthMiddleware($config);
        $stack->push($oauth, 'oauth');

        return $stack;
    }

    /**
     * Initializes the oauth middleware.
     *
     * @param array $config
     *   Holds the configuration to initialize the service client.
     *
     * @return \kamermans\OAuth2\OAuth2Middleware
     */
    private function initializeOAuthMiddleware(array $config)
    {
        // Authorization client - this is used to request OAuth access tokens
        $reauth_client = new Client([
          'base_uri' => $config['access_token_url'],
        ]);

        $reauth_config = [
          'client_id'     => $config['client_id'],
          'client_secret' => $config['client_secret'],
        ];

        $grantType = new ClientCredentials($reauth_client, $reauth_config);
        $oauth = new OAuth2Middleware($grantType);

        // Set custom page planner signer.
        $signer = new PageplannerSigner();
        $oauth->setClientCredentialsSigner($signer);
        return $oauth;
    }

}
