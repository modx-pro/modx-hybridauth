## HybridAuth for MODX Revolution

Social login for MODX Revolution via [Hybridauth](https://github.com/hybridauth/hybridauth) **3.13**.

**PHP:** 7.4–8.4

### Build

Before building the transport package:

```bash
cd core/components/hybridauth
composer install
```

`vendor/` is not committed; the package must include it after `composer install`.

### Providers

Built-in Hybridauth providers (Google, Facebook, GitHub, Twitter/X, …) work through `ha.keys.{Name}` JSON settings.

Local classmap providers (restored after Hybridauth 3.8.2 removed them):

- **Yandex** — `{"keys":{"id":"...","secret":"..."}}`
- **Vkontakte** (legacy OAuth) — `{"keys":{"id":"...","secret":"..."},"scope":"email"}`. New apps: [VK ID](https://dev.vk.com/ru/vkid) ([#56](https://github.com/modx-pro/modx-hybridauth/issues/56)).
- **Odnoklassniki** — `{"keys":{"id":"...","key":"...","secret":"..."}}` (`key` = application_key). See [#52](https://github.com/modx-pro/modx-hybridauth/issues/52).

**MailRu** is not shipped; use VK ID for Mail.ru accounts.

Callback URL for each provider: `{site_url}?hauth.done={ProviderName}`.

### Issues

https://github.com/modx-pro/modx-hybridauth/issues
