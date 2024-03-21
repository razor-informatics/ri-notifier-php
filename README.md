# Razor Informatics Notifier PHP SDK


> This SDK provides easier work with Razor Informatics Notifier API for applications written in PHP.

## POSTMAN Collection

[<img src="https://run.pstmn.io/button.svg" alt="Run In Postman" style="width: 128px; height: 32px;">](https://app.getpostman.com/run-collection/4421476-caefe8a2-77dc-4323-bd26-11f2df86946f?action=collection%2Ffork&source=rip_markdown&collection-url=entityId%3D4421476-caefe8a2-77dc-4323-bd26-11f2df86946f%26entityType%3Dcollection%26workspaceId%3D159f2a8d-aabb-46d7-a6e3-9912793cede7)

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

### Get Gateway Balance Example

Get the account balance of gateway selected when available.
Available gateways are

- Notifier (project balance)
- Celcom Africa
- Emreign
- Africa’s Talking
- Onfon Media
- Web SMS
- _more coming soon._

```php
use RazorInformatics\RiNotifierPhp;

$apiKey  = 'YOUR_API_KEY';
$razor = new RiNotifierPhp\Notifier($apiKey);

$results = $razor->gateway(Constants::GATEWAY_NOTIFIER)->details();

print_r($results);
```
