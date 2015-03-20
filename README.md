# yarakuzen-api-php-client
A MIT licensed PHP client library for the YarakuZen API.

```php
<?php
include "yarakuzen.lib.php";

/**
 * create a new client, which can be used in multiple requests.
 * the public and private key can be generated at YarakuZen settings page.
 */
$client = new Client($publicKey, $privateKey);

$t1 = new TextData();
$t1->machineTranslate();
$t1->customData(123);
$t1->text("This is the text that requires translation");

$t2 = new TextData();
$t2->machineTranslate();
$t2->customData(12);
$t2->text("This is another text that requires translation");

$t3 = new TextData();
$t3->machineTranslate();
$t3->customData(1);
$t3->text("This is yet another text that requires translation");

$r = new RequestPayload();
$r->lcSrc("en")->lcTgt("ja")->persist()->machineTranslate()->addText($t1)->addText($t2)->addText($t3);


$r = $client->callTexts($r);

?>
```
