# yarakuzen-api-php-client
A PHP client library for the YarakuZen API.

```php
<?php
include "yaraku.lib.php";

$yaraku = new YarakuClient("your key", "your secret");
$yaraku->from("jp")->to("en")->translate("すごいですよ！");

?>
```
