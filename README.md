# Razor Informatics Notifier PHP SDK


> This SDK provides easier work with Razor Informatics Notifier API for applications written in PHP.

## POSTMAN Collection

[<img src="https://run.pstmn.io/button.svg" alt="Run In Postman" style="width: 128px; height: 32px;">](https://app.getpostman.com/run-collection/4421476-caefe8a2-77dc-4323-bd26-11f2df86946f?action=collection%2Ffork&source=rip_markdown&collection-url=entityId%3D4421476-caefe8a2-77dc-4323-bd26-11f2df86946f%26entityType%3Dcollection%26workspaceId%3D159f2a8d-aabb-46d7-a6e3-9912793cede7)

## Documentation
To get the depth details of the api check [API docs here](https://notifier.razorinformatics.co.ke).

## Requirements

- PHP **8.3** or newer
- [Guzzle](https://github.com/guzzle/guzzle) 7.10+ or 8.x (installed automatically by Composer)

> **Note:** PHP 8.1 reached end of life on 31 Dec 2025, and PHP 8.2 only gets security fixes until it reaches end of life on 31 Dec 2026.
> Starting with this release, the SDK requires PHP 8.3+. If you are still on PHP 8.1/8.2, pin `razor-informatics/ri-notifier-php:^0.2` until you upgrade.

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
        'phone_number' => '0700123456',
        'message' => "Howdy welcome to the team"
]);

print_r($results);
```

`priority` is optional and defaults to a notification. Use a `MessagePriority` case: `Marketing` (1), `Notification` (2), `Transactional` (3) or `HighPriority` (4). A plain int also works. High priority needs to be enabled for the project, otherwise it goes as transactional.

```php
$results = $razor->message()->send([
        'phone_number' => '0700123456',
        'message' => 'Your verification code is 482913',
        'priority' => RiNotifierPhp\MessagePriority::Transactional,
]);
```

### Send to Many Example

Send one message to every number of `phone_number`, `contacts` and the contacts of `labels`, once each. Each field takes a string or an array of them, with at most 1000 numbers per request. If you leave out `priority`, a send to contacts or labels goes as marketing and any other send goes as a notification.

```php
use RazorInformatics\RiNotifierPhp;

$apiKey  = 'YOUR_API_KEY';
$razor = new RiNotifierPhp\Notifier($apiKey);

$results = $razor->message()->sendMany([
        'message' => 'Our offices are closed on Friday, 10 October.',
        'phone_number' => ['0712345678', '+254722000111'],
        'contacts' => ['CONTACT ID'],
        'labels' => ['LABEL ID'],
]);

print_r($results);
```

On success, `data` holds the created messages. The result also carries the server's `message`, a `summary` (recipients, statuses and failed count) and the `failed` recipients.

### Send Personalised Messages Example

An array of messages gives each recipient its own message. It pairs by position with an array of `phone_number` or of `contacts` of the same length. It cannot go to labels.

```php
$results = $razor->message()->sendMany([
        'message' => [
            'Hi Jane, your order #1042 has shipped.',
            'Hi John, your order #1043 has shipped.',
        ],
        'phone_number' => ['0712345678', '0722000111'],
        'priority' => RiNotifierPhp\MessagePriority::Transactional,
]);
```

### Fetch message Example
details of a previous sent message.

```php
use RazorInformatics\RiNotifierPhp;

$apiKey  = 'YOUR_API_KEY';
$razor = new Notifier($apiKey);


$results = $razor->message()->fetchMessage('MESSAGE ID');

print_r($results);
```
### Decode Mpesa Hash Example

Decode a hash to retrieve the original message details.

```php
use RazorInformatics\RiNotifierPhp;

$apiKey  = 'YOUR_API_KEY';
$razor = new Notifier($apiKey);


$results = $razor->decoder()->decode('HASH_VALUE');

print_r($results);
```

On success, this will return:

```php
[
    'status' => 'success',
    'data' => [
        'phone_number' => '254700100100',
        'charged' => 0.1
    ]
]
```

On failure, it will return:

```php
[
    'status' => 'error',
    'message' => 'Error message here',
    'data' => []
]
```

### Contacts Example

The project address book. A phone number can only be used once in the project.

```php
use RazorInformatics\RiNotifierPhp;

$apiKey  = 'YOUR_API_KEY';
$razor = new Notifier($apiKey);

// a page of contacts with their labels, `links` and `meta` carry the pagination
$results = $razor->contacts()->list(page: 1, perPage: 15);

// `name` and `labels` are optional
$results = $razor->contacts()->create([
        'name' => 'Jane Wanjiku',
        'phone' => '0712345678',
        'labels' => ['LABEL ID'],
]);

$results = $razor->contacts()->get('CONTACT ID');

// only the fields given change, `labels` replaces the contact labels ([] removes them all)
$results = $razor->contacts()->update('CONTACT ID', [
        'name' => 'Jane W. Kamau',
        'labels' => [],
]);

// its labels stay
$results = $razor->contacts()->delete('CONTACT ID');
```

### Labels Example

Groups of contacts. You can send to a whole label in one request. Label names are unique in the project, with a maximum of 50 characters.

```php
use RazorInformatics\RiNotifierPhp;

$apiKey  = 'YOUR_API_KEY';
$razor = new Notifier($apiKey);

// a page of labels with their contact counts
$results = $razor->labels()->list(page: 1, perPage: 15);

$results = $razor->labels()->create('Customers');

$results = $razor->labels()->get('LABEL ID');

$results = $razor->labels()->rename('LABEL ID', 'VIP Customers');

// its contacts stay
$results = $razor->labels()->delete('LABEL ID');
```

### Errors

On failure every method returns `status` `error`. For a validation error (422), a missing resource (404) or an insufficient balance (402), the `message` is the one the server gives, and `data` holds the validation errors keyed by field:

```php
[
    'status' => 'error',
    'message' => 'a contact with this phone number already exists.',
    'data' => [
        'phone' => ['a contact with this phone number already exists.']
    ]
]
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
- Roam Tech
- Razor SMS
- _more coming soon._

```php
use RazorInformatics\RiNotifierPhp;

$apiKey  = 'YOUR_API_KEY';
$razor = new Notifier($apiKey);

$results = $razor->gateway(RiNotifierPhp\Constants::GATEWAY_NOTIFIER)->details();

print_r($results);
```
