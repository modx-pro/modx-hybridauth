## HybridAuth for MODX Revolution

Social login for MODX Revolution via [Hybridauth](https://github.com/hybridauth/hybridauth) **3.13**.

**PHP:** 7.4–8.4

### Build

`vendor/` is gitignored. `_build/build.transport.php` runs `composer install --no-dev`
and fails the build if `vendor/autoload.php` is missing (#54). `composer.lock` is committed.

### Providers

Built-in Hybridauth providers (Google, Facebook, GitHub, X, Yahoo, …) work through `ha.keys.{Name}` JSON settings. Prefer **X** (OAuth 2); `Twitter` remains as legacy OAuth 1.0a.

- **Yahoo** — create an app at [developer.yahoo.com/apps](https://developer.yahoo.com/apps/), enable OpenID Connect Permissions, set `ha.keys.Yahoo` to `{"keys":{"id":"...","secret":"..."},"scope":"profile"}`, use `&providers=`Yahoo``.

Local classmap providers:

- **VkId** (OAuth 2.1 + PKCE) — register in [VK ID](https://id.vk.com/about/business/go), set `ha.keys.VkId` to `{"keys":{"id":"...","secret":"..."},"scope":"vkid.personal_info email"}`, use `&providers=`VkId``. See [#56](https://github.com/modx-pro/modx-hybridauth/issues/56).
- **Yandex** — `{"keys":{"id":"...","secret":"..."}}`
- **Vkontakte** (legacy `api.vk.com` OAuth) — only for old apps; prefer **VkId**.
- **Odnoklassniki** — `{"keys":{"id":"...","key":"...","secret":"..."}}` (`key` = application_key). See [#52](https://github.com/modx-pro/modx-hybridauth/issues/52).

**MailRu** is not shipped; VK ID can cover Mail.ru accounts in one OAuth flow.

Callback URL for each provider: `{site_url}?hauth_done={ProviderName}` (underscore; required for Facebook).

### Post hooks (#31)

After OAuth signup/login you can run snippets or plugins:

```
[[!HybridAuth?providers=`X` &postHooks=`hookNewsletter`]]
```

Snippet properties: `user`, `userid`, `provider`, `profile`, `ha_mode` (`register` or `login`).

System events: `OnHAUserCreate`, `OnHAUserLogin`, `OnHAUserBind` (payload includes the same fields).

### Security

- Register the **exact** callback URI in each IdP app (HTTPS on TLS sites).
- Do not host open redirects on the same domain as `site_url` ([Covert Redirect](https://www.phpclasses.org/blog/package/7700/post/4-Is-Your-OAuth-20-Application-Secure.html) / [#25](https://github.com/modx-pro/modx-hybridauth/issues/25)).
- `&redirectUri=` must be same-origin as `site_url`; off-site values are ignored.
- Hybridauth uses authorization code + OAuth2 `state`.

### Issues

https://github.com/modx-pro/modx-hybridauth/issues
