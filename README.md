# Embryo Logger
Minimalist and fast PSR-3 Stream logger.

## Requirements
* PHP >= 7.1
* A [PSR-7](https://www.php-fig.org/psr/psr-7/) http message implementation and [PSR-17](https://www.php-fig.org/psr/psr-17/) http factory implementation (ex. [Embryo-Http](https://github.com/davidecesarano/Embryo-Http))

## Installation
Using Composer:
```
$ composer require davidecesarano/embryo-logger
```

## Usage
Set `log` directory and create `logger` object. You can set `emergency`, `alert`, `critical`, `error`, `warning`, `notice`, `info`, `debug` and `log` message.
```php
use Embryo\Log\StreamLogger;

$logPath = __DIR__.DIRECTORY_SEPARATOR.'logs';
$logger  = new StreamLogger($logPath);

$message = "User {username} created";
$context = ['username' => 'bolivar'];

// info log
$logger->info($message, $context);
```
This example writes `User bolivar created` in the `info.log` file.

You may quickly test this using the built-in PHP server going to http://localhost:8000.
```
$ cd example
$ php -S localhost:8000
```