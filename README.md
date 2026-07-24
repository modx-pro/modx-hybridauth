## HybridAuth for MODX Revolution

Social login for MODX Revolution via [Hybridauth](https://github.com/hybridauth/hybridauth) **3.13**.

**PHP:** 7.4–8.4

### Build

`vendor/` is gitignored. `_build/build.transport.php` runs `composer install --no-dev`
and fails the build if `vendor/autoload.php` is missing (#54). `composer.lock` is committed.

### Providers

Built-in Hybridauth providers (Google, Facebook, GitHub, X, Yahoo, …) work through `ha.keys.{Name}` JSON settings. Prefer **X** (OAuth 2); `Twitter` remains as legacy OAuth 1.0a.

- **Yahoo** — create an app at [developer.yahoo.com/apps](https://developer.yahoo.com/apps/), enable OpenID Connect Permissions, set `ha.keys.Yahoo` to `{"keys":{"id":"...","secret":"..."},"scope":"profile"}`, use `&providers=`Yahoo``.

Local classmap providers (restored after Hybridauth 3.8.2 removed them):

- **Yandex** — `{"keys":{"id":"...","secret":"..."}}`
- **Vkontakte** (legacy OAuth) — `{"keys":{"id":"...","secret":"..."},"scope":"email"}`. New apps: [VK ID](https://dev.vk.com/ru/vkid) ([#56](https://github.com/modx-pro/modx-hybridauth/issues/56)).
- **Odnoklassniki** — `{"keys":{"id":"...","key":"...","secret":"..."}}` (`key` = application_key). See [#52](https://github.com/modx-pro/modx-hybridauth/issues/52).

**MailRu** is not shipped; use VK ID for Mail.ru accounts.

Callback URL for each provider: `{site_url}?hauth_done={ProviderName}` (underscore; required for Facebook).

### Security

- Register the **exact** callback URI in each IdP app (HTTPS on TLS sites).
- Do not host open redirects on the same domain as `site_url` ([Covert Redirect](https://www.phpclasses.org/blog/package/7700/post/4-Is-Your-OAuth-20-Application-Secure.html) / [#25](https://github.com/modx-pro/modx-hybridauth/issues/25)).
- `&redirectUri=` must be same-origin as `site_url`; off-site values are ignored.
- Hybridauth uses authorization code + OAuth2 `state`.

### Issues

https://github.com/modx-pro/modx-hybridauth/issues
