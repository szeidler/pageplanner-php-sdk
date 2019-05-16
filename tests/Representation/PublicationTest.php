<?php

namespace szeidler\Pageplanner\Tests\Representation;

use GuzzleHttp\Command\Exception\CommandClientException;
use GuzzleHttp\Command\ResultInterface;
use szeidler\Pageplanner\Tests\PageplannerTestWrapper;

/**
 * Tests the Publication response model.
 *
 * @package szeidler\Pageplanner\Tests\Representation
 */
class PublicationTest extends PageplannerTestWrapper
{

    /**
     * Tests, that the Publications request returns a valid response.
     */
    public function testGetPublications()
    {
        $response = $this->client->getPublications();
        $this->assertInstanceOf(ResultInterface::class, $response,
          'The response is not a proper Guzzle result.');

        $this->assertGreaterThan(0, $response->getIterator()->count(),
          'The publications response be an iterator with at least one item.');

        $this->assertArrayHasKey('id', $response->offsetGet(0),
          'The publication should have an id.');

        $this->assertArrayHasKey('name', $response->offsetGet(0),
          'The publication should have a name.');
    }

}
