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
$t1 = new TextData();
$t1->machineTranslate();
$t1->customData(123);
$t1->text("This is the text that requires translation");

// another text..
$t2 = new TextData();
$t2->machineTranslate();
$t2->customData(12);
$t2->text("This is another text that requires translation");

// you can put as many as you want in the same request
$t3 = new TextData();
// showing another syntax for the same kind of work
$t3->machineTranslate()->customData(1)->text("This is yet another text that requires translation");


/*
 * Next we create the payload.
 */
$r = new RequestPayload();
// that's how we set the properties; every method return the actuall object, so you can chain methods like this
$r->lcSrc("en")->lcTgt("ja")->persist()->machineTranslate()->addText($t1)->addText($t2)->addText($t3);


/*
 * The last part is the actuall call. The response will be a valid PHP object representing the response from the server,
 * even if it is an error message.
 */
$resp = $client->callTexts($r);

?>
```
