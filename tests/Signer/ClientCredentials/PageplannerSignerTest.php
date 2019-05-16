<?php

namespace szeidler\Pageplanner\Tests\Signer\ClientCredentials;

use GuzzleHttp\Psr7\Request;
use function GuzzleHttp\Psr7\stream_for;
use PHPUnit\Framework\TestCase;
use szeidler\Pageplanner\Signer\ClientCredentials\PageplannerSigner;

class PageplannerSignerTest extends Testcase
{

    /**
     * Test that the client id, secret and resource is part of the request.
     */
    public function testSign()
    {
        $clientId = 'foo';
        $clientSecret = 'bar';
        $resource = 'https://myresource.com/PagePlanner.Web.PublicAPI';

        $clientIdFieldName = 'client_id';
        $clientSecretFieldName = 'client_secret';
        $resourceFieldName = 'resource';

        $request = new Request('GET', '/', [
          'Content-Type' => 'application/x-www-form-urlencoded',
        ]);

        $stream = stream_for('');
        $request = $request->withBody($stream);

        $signer = new PageplannerSigner();
        $signer->setResource($resource);
        $request = $signer->sign($request, $clientId, $clientSecret);

        $this->assertEquals($clientId, $this->getFormPostBodyValue($request, $clientIdFieldName));
        $this->assertEquals($clientSecret, $this->getFormPostBodyValue($request, $clientSecretFieldName));
        $this->assertEquals($resource, $this->getFormPostBodyValue($request, $resourceFieldName));
    }

    /**
     * Helper function to get the form post values from the request body.
     *
     * @param $request
     * @param $field
     *
     * @return mixed|null
     *
     * @see \kamermans\OAuth2\Tests\BaseTestCase::getFormPostBodyValue()
     */
    protected function getFormPostBodyValue($request, $field)
    {
        $query_string = (string)$request->getBody();

        $values = $this->parseQueryString($query_string);

        return array_key_exists($field, $values) ? $values[$field] : null;
    }

    /**
     * Helper function to parse a query string into an array.
     *
     * @param $query_string
     *
     * @return array
     *
     * @see \kamermans\OAuth2\Tests\BaseTestCase::parseQueryString()
     */
    protected function parseQueryString($query_string)
    {
        $values = [];
        foreach (explode('&', $query_string) as $component) {
            list($key, $value) = explode('=', $component);
            $values[rawurldecode($key)] = rawurldecode($value);
        }

        return $values;
    }
}
