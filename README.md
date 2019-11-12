# Xtra
Xtra Php Classes

### Zaktualizuj zależności (dev)
```bash
composer update
composer dump-autoload
composer dump-autoload -o
```

### Dodaj do swojego projektu
```bash
# Zobacz, wyszukaj
composer show moovspace/xtra
composer search moovspace/xtra

# Dodaj
composer require moovspace/xtra:~1.0

# Dodaj do composer.json
{
    "require": {
        "moovspace/xtra": "~1.0"
    }
}

# Lub dodaj
"repositories": [        
        {
            "type": "vcs",
            "url": "https://github.com/moovspace/xtra"
        }
],
```

### Użycie biblioteki w projekcie
```php
<?php
require_once('/vendor/autoload.php');

// import class
use Xtra\Curl\CurlClient;

// Display errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

try {

    // Use class and catch "errors"
    $curl = new CurlClient();
    // Host
    $curl->AddUrl("http://domain.here/curl-test.php");

    // Method POST
    $curl->SetMethod("POST");

    // Data
    $curl->AddData("username","Max");
    $curl->AddData("email","ho@email.xx");

    // Add file
    $curl->AddFile("router.php");

    // Force ssl
    $curl->SetAllowSelfsigned();

    // Send as Json
    // $curl->SetJson();

    // Send
    echo $curl->Send();
    
}catch(Exception $e){
    // echo $e->getMessage();
    // echo $e->getCode();
    print_r($e);
}
?>
```
