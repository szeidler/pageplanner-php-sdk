<?php

namespace szeidler\Pageplanner\Tests\Representation;

use GuzzleHttp\Command\ResultInterface;
use GuzzleHttp\Psr7\Response;
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
        $responses = [new Response(200, [], file_get_contents(__DIR__ . '/fixtures/publications.json'))];
        $client = $this->getMockedPageplannerClient($responses);

        $response = $client->getPublications();
        $this->assertInstanceOf(ResultInterface::class, $response,
          'The response is not a proper Guzzle result.');

        $this->assertGreaterThan(0, $response->getIterator()->count(),
          'The publications response be an iterator with at least one item.');

        $this->assertArrayHasKey('id', $response->offsetGet(0),
          'The publication should have an id.');

        $this->assertArrayHasKey('name', $response->offsetGet(0),
          'The publication should have a name.');
    }

    /**
     * Tests, that the single Publication request returns a valid response.
     */
    public function testGetPublication()
    {
        $responses = [new Response(200, [], file_get_contents(__DIR__ . '/fixtures/publication.json'))];
        $client = $this->getMockedPageplannerClient($responses);

        $response = $client->getPublications(['id' => 18]);
        $this->assertInstanceOf(ResultInterface::class, $response,
          'The response is not a proper Guzzle result.');

        $this->assertNotEmpty($response->offsetGet('name'),
          'The publication should have a name.');

        $this->assertNotEmpty($response->offsetGet('publicationCode'),
          'The publication should have a publicationcode.');
    }

}
