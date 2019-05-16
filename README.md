# Pageplanner PHP SDK

[![Build Status](https://travis-ci.org/szeidler/pageplanner-php-sdk.svg?branch=master)](https://travis-ci.org/szeidler/pageplanner-php-sdk)

Pagelanner PHP SDK utilizes [guzzle-services](https://github.com/guzzle/guzzle-services) for an easy integration with
[Pageplanner's](http://pageplannersolutions.com/) API.

## Requirements

* PHP 7.0 or greater
* Composer
* Guzzle

## Installation

Add Pageplanner PHP SDK as a composer dependency.

`composer require szeidler/pageplanner-php-sdk:^1.0`

## Usage

Returns the asset representation based on the resource url of the asset.

```php
use szeidler\Pageplanner\PageplannerClient;

$client = new PageplannerClient([
    'baseUrl' => 'https://preportal.pageplanner.no/api-demo/api/',
    'access_token_url' => 'https://login.microsoftonline.com/pageplannersolutions.com/oauth2/token',
    'client_id'        => 'xxxxxx-xxxxxxxxxxxx',
    'client_secret'    => '123456',
]);

// Get all issues.
$issues = $client->getIssues();
foreach ($issues->getIterator() as $issue) {
  print $issue['name']';
}

// Get a issue by id.
$issue = $client->getIssue(['id' => 2]);
print $issue->offsetGet('name');

// Get all publications.
$publications = $client->getPublications();
foreach ($publications->getIterator() as $publication) {
  print $publication['name'];
}

// Get a single publication by id.
$publications = $client->getPublications(['id' => 2]);
print $publication->offsetGet('name');
print $publication->offsetGet('publicationCode');

// Get all stories for an issue.
$stories = $client->getStories(['issueId' => 5]);
foreach ($stories->getIterator() as $story) {
  print $story['name'];
}

// Get a single story by id.
$story = $client->getStory(['id' => 123]);
print $story->offsetGet('name');
print $story->offsetGet('author');

// Create a story for an issue.
$data = [
    'id' => 'abcd',
    'title' =>
      [
        'value' => 'My new title',
        'isHtml' => FALSE,
      ],
    'abstract' =>
      [
        'value' => 'Put your abstract here',
        'isHtml' => FALSE,
      ],
    'body' =>
      [
        'value' => '<strong>Your body could include HTML</strong>',
        'isHtml' => TRUE,
      ],
    'tags' => ['News', 'Culture'],
];
$story = $client->createStory($data);
print $story->offsetGet['name'];
```

## Testing

This SDK includes PHPUnit as a composer `require-dev`. Copy the `phpunit.xml.dist` to `phpunit.xml` and fill in with
your API testing credentials.

`./vendor/bin/phpunit -c phpunit.xml`

## Credits

Stephan Zeidler for [Ramsalt Lab AS](https://ramsalt.com)

## License

The MIT License (MIT)
