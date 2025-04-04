# Yaraku Translate - Translate Api v2
A MIT licensed PHP thin client library for the Yaraku Translate's Translate Api v2

## Install with composer
```text
composer require yaraku/translate-api-v2
```
## Set up
```php
<?php

use YarakuTranslateTranslateApiV2\Client

/*
 * Create a new client, which can be used in multiple requests.
 * The Authorization key can be found in:
 * Yaraku Translate -> Settings -> API -> TranslateAPI V2.
 * 
 * An optional url can be passed in to point to the sandbox for testing.
 */
$client = new Client($authkey, ?$customUrl);
```
### POST Request
```php
/*
 * Call translate with an array of texts you want to translate.
 * Set the text (source) and translation (target) languages.
 */
$result = $client->translate(
    ['This is a test.', 'Add your texts here.'],
    'en',
    'ja'
);
```
