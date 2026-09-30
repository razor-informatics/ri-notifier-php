# Changelog

Releases will be listed here.

## 0.3.0 _2026-09-30_
PHP 8.3 and Guzzle 8 support
- **Breaking:** minimum PHP version is now 8.3 (PHP 8.1 is end of life; PHP 8.2 reaches end of life on 31 Dec 2026).
- Allow `guzzlehttp/guzzle` `^7.10 || ^8.2`.
- Modernise the codebase: `strict_types`, typed class constants, readonly promoted properties, `match` for error messages, and `ResponseInterface` typing.
- `Notifier::gateway()` now type-hints its `$gateway` argument as `string`.

Notifier v2.7.0 APIs
- `Message::send()` accepts an optional `priority`, given as a case of the new `MessagePriority` enum or as an int.
- Add `Message::sendMany()` (alias `bulk()`) for `v2/message/send`. It sends to many phone numbers, contacts and labels, or sends personalised messages. The result also carries `message`, `summary` and `failed`.
- Add `Notifier::contacts()` (`Contact`), which can list, create, get, update and delete contacts.
- Add `Notifier::labels()` (`Label`), which can list, create, get, update/rename and delete labels.
- List results carry the pagination `links` and `meta`. A `204 No Content` response is returned as success with empty data.
- Add the `Constants::GATEWAY_ROAM_TECH` (`roam`) and `Constants::GATEWAY_RAZOR_SMS` (`razor-sms`) gateways.
- Errors for 402, 404 and 422 now use the server's message, and `data` holds the validation `errors` keyed by field.
- Remove the incorrect default `Content-Type: multipart/form-data` header. Each request sets its own.

## 0.2.0 _2026-05-05_
Add mpesa hash decode  functionality
- Introduce `decode` method in `DeHash` class for hash decoding.
- Update `README.md` with an example demonstrating decode functionality.
- Add `decoder` method to `Notifier` for instantiating `DeHash`._

## 0.1.1 _2023-01-23_

- Add constants.
- Error handling.

## 0.1.0 _2022-08-11_

### initial release

- Send Messages.
- Check Message status.
- Check account balance.