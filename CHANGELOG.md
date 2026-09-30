# Changelog

Releases will be listed here.

## 0.3.0 _Unreleased_
PHP 8.3 and Guzzle 8 support
- **Breaking:** minimum PHP version is now 8.3 (PHP 8.1 is end of life; PHP 8.2 reaches end of life on 31 Dec 2026).
- Allow `guzzlehttp/guzzle` `^7.10 || ^8.2`.
- Modernise the codebase: `strict_types`, typed class constants, readonly promoted properties, `match` for error messages, and `ResponseInterface` typing.
- `Notifier::gateway()` now type-hints its `$gateway` argument as `string`.

## 0.2.0 _2026-05-05
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