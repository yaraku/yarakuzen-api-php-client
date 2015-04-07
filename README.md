# yarakuzen-api-php-client
A MIT licensed PHP thin client library for the YarakuZen API.

```php
<?php
include "yarakuzen.lib.php";

/*
 * create a new client, which can be used in multiple requests.
 * the public and private key can be generated at YarakuZen settings page.
 */
$client = new Client($publicKey, $privateKey);


/*
 * Next we create some texts that need to be translated
 */
$t1 = new YarakuZenApi\TextData();
$t1->customData(123);
$t1->text("This is the text that requires translation");

// another text..
$t2 = new YarakuZenApi\TextData();
$t2->customData(12);
$t2->text("This is another text that requires translation");

// you can put as many as you want in the same request
$t3 = new YarakuZenApi\TextData();
// showing another syntax for the same kind of work
$t3->customData(1)->text("This is yet another text that requires translation");


/*
 * Next we create the payload.
 */
$r = new YarakuZenApi\RequestPayload();
// that's how we set the properties; every method return the actual object, so you can chain methods like this
$r->lcSrc("en")->lcTgt("ja")->persist()->machineTranslate()->addText($t1)->addText($t2)->addText($t3);


/*
 * The last part is the actual call. The response will be a valid PHP object representing the response from the server,
 * even if it is an error message.
 */
$resp = $client->callTexts($r);

?>
```
