# yarakuzen-api-php-client
A PHP client library for the YarakuZen API.

```php
<?php
include "yarakuzen.lib.php";

$yaraku = new YarakuZenClient("your key", "your secret");
$yaraku->from("jp")->to("en")->translate("すごいですよ！");

?>
```
