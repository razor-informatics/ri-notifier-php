# Razor Informatics Notifier PHP SDK


> This SDK provides easier work with Razor Informatics Notifier API for applications written in PHP.

## Documentation
To get the depth details of the api check [API docs here](https://notifier.razorinformatics.co.ke).

## Install

You can install the PHP SDK via composer or by downloading the source

#### Via Composer

The recommended way to install the SDK is with [Composer](http://getcomposer.org/).

```bash
composer require razor-informatics/ri-notifier-php
```

## Usage

The SDK needs to be instantiated using your API key, which you can get from the project settings [here](https://notifier.razorinformatics.co.ke/dashboard).

### Send  Message Example

```php
use RazorInformatics\RiNotifierPhp;

$apiKey  = 'YOUR_API_KEY';
$razor = new RiNotifierPhp\Notifier($apiKey);


$results = $razor->message()->send([
        'phone_number' => 0700XXXYYY,
        'message' => "Howdy welcome to the team"
]);

print_r($results);
```

### Fetch message Example
details of a previous sent message.

```php
use RazorInformatics\RiNotifierPhp;

$apiKey  = 'YOUR_API_KEY';
$razor = new RiNotifierPhp\Notifier($apiKey);


$results = $razor->message()->fetchMessage('MESSAGE ID');

print_r($results);
```
### Get Account Details Example

The data available is project details & current account balance

```php
use RazorInformatics\RiNotifierPhp;

$apiKey  = 'YOUR_API_KEY';
$razor = new RiNotifierPhp\Notifier($apiKey);


$results = $razor->account()->getDetails();

print_r($results);
```