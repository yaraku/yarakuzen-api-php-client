# yarakuzen-api-php-client-v2
A MIT licensed PHP thin client library for the YarakuZen APIV2.

## Set up

```php
<?php

use YarakuZenTranslateApi\Client

/*
 * create a new client, which can be used in multiple requests.
 * the Authorization key can be found in:
 * YarakuZen -> Settings -> API -> TranslateAPI V2
 * an optional url can be used to point to the sandbox for testing.
 */
$client = new Client($authkey, ?$customUrl);
```

### POST Request
```php
/*
 * Call translate with an array of texts you want to translate.
 * Set the text (source) and translation(target) languages
 */
 
$result = $client->translate(
    ['This is a test.', 'Add your texts here'],
    'en',
    'ja'
);
```
