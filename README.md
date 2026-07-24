## HybridAuth for MODX Revolution

Social login for MODX Revolution via [Hybridauth](https://github.com/hybridauth/hybridauth) **3.13**.

**PHP:** 7.4–8.4

### Build

`vendor/` is gitignored. `_build/build.transport.php` runs `composer install --no-dev`
and fails the build if `vendor/autoload.php` is missing (#54). `composer.lock` is committed.

### Providers

Built-in Hybridauth providers (Google, Facebook, GitHub, X, …) work through `ha.keys.{Name}` JSON settings. Prefer **X** (OAuth 2); `Twitter` remains as legacy OAuth 1.0a.

Local classmap providers (restored after Hybridauth 3.8.2 removed them):

- **Yandex** — `{"keys":{"id":"...","secret":"..."}}`
- **Vkontakte** (legacy OAuth) — `{"keys":{"id":"...","secret":"..."},"scope":"email"}`. New apps: [VK ID](https://dev.vk.com/ru/vkid) ([#56](https://github.com/modx-pro/modx-hybridauth/issues/56)).
- **Odnoklassniki** — `{"keys":{"id":"...","key":"...","secret":"..."}}` (`key` = application_key). See [#52](https://github.com/modx-pro/modx-hybridauth/issues/52).

**MailRu** is not shipped; use VK ID for Mail.ru accounts.

Callback URL for each provider: `{site_url}?hauth_done={ProviderName}` (underscore; required for Facebook).

### Issues

https://github.com/modx-pro/modx-hybridauth/issues
