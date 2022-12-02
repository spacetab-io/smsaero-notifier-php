SmsAero Notifier
================

Provides [SmsAero](https://smsaero.ru/) integration for Symfony Notifier.

DSN example
-----------

```
SMSAERO_DSN=smsaero://EMAIL:API_KEY@default?from=FROM&channel=CHANNEL
```

where:
- `EMAIL` account email 
- `API_KEY` is a secret key from [settings page](https://smsaero.ru/cabinet/settings/apikey/) 
- `FROM` is your sender (NB: text identity, not a phone number)
- `CHANNEL` channel type, default: INTERNATIONAL

Resources
---------

* [Contributing](https://symfony.com/doc/current/contributing/index.html)
* [Report issues](https://github.com/symfony/symfony/issues) and
  [send Pull Requests](https://github.com/symfony/symfony/pulls)
  in the [main Symfony repository](https://github.com/symfony/symfony)
