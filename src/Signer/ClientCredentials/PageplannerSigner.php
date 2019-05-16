<?php

namespace szeidler\Pageplanner\Signer\ClientCredentials;

use function GuzzleHttp\Psr7\stream_for;
use kamermans\OAuth2\Signer\ClientCredentials\PostFormData;
use kamermans\OAuth2\Utils\Helper;

/**
 * Adds the resource for a proper page planner authentication request.
 *
 * @package szeidler\Pageplanner\Signer\ClientCredentials
 */
class PageplannerSigner extends PostFormData
{

    /**
     * @var string
     */
    protected $resource = 'https://pageplannersolutions.com/PagePlanner.Web.PublicAPI';

    /**
     * Sets the resource.
     *
     * @param string $resource
     */
    public function setResource($resource)
    {
        $this->resource = $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function sign($request, $clientId, $clientSecret)
    {
        $request = parent::sign($request, $clientId, $clientSecret);

        if (Helper::guzzleIs('>=', 6)) {
            parse_str($request->getBody(), $data);
            $data['resource'] = $this->resource;

            $body_stream = stream_for(http_build_query($data, '', '&'));
            return $request->withBody($body_stream);
        }

        $body = $request->getBody();
        $body->setField('resource', $this->resource);

        return $request;
    }
}
