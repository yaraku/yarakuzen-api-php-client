# Yaraku Translate - Translate API v2
A MIT licensed PHP thin client library for the Yaraku Translate's Translate API v2

## Install with composer
```text
composer require yaraku/translate-api
```
## Set up
```php
<?php

use YarakuTranslate\TranslateApiV2\Client

/*
 * Create a new client, which can be used in multiple requests.
 * 
 * The Authorization key can be found in:
 * Yaraku Translate -> Settings -> API -> TranslateAPI V2.
 * 
 * The url will be the API endpoint you are using, for example:
 * https://translate.yaraku.com/api/translate/v2
 */
 
$client = new Client($authkey, $url);
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
