--------------------
HybridAuth
--------------------
Author: Vasiliy Naumkin <bezumkin@yandex.ru>
--------------------
MODX Revolution package for Hybridauth
https://github.com/hybridauth/hybridauth (3.13).

PHP: 7.4–8.4

Social login for providers such as Facebook, X, LinkedIn, Google, Yahoo, and VK ID.

Issues and ideas:
https://github.com/modx-pro/modx-hybridauth/issues

--------------------
Installation
--------------------
Install from the MODX package manager.

1. Create an app at the provider (example: X at https://developer.x.com/).
2. In System Settings → hybridauth, set ha.keys.{Provider}. Example for X: ha.keys.X
3. Put keys as JSON:

   {"keys":{"id":"your id","secret":"your secret"}}

   X (OAuth 2) with scope:
   {"keys":{"id":"...","secret":"..."},"scope":"tweet.read users.read users.email offline.access"}

   Legacy Twitter OAuth 1.0a (consumer key, not id):
   {"keys":{"key":"...","secret":"..."}}

   Yahoo (enable OpenID Connect Permissions):
   {"keys":{"id":"...","secret":"..."},"scope":"profile"}

   Odnoklassniki (public application_key as "key"):
   {"keys":{"id":"...","key":"...","secret":"..."}}

   Register the same callback in the provider cabinet:
   {site_url}?hauth_done={Provider}
   Use an underscore (Facebook rejects hauth.done). On TLS sites, site_url must be https.

4. Call the snippet on a page:
   [[!HybridAuth?providers=`X`]]
   or
   [[!HybridAuth?providers=`Yahoo`]]

Post hooks (#31):
  [[!HybridAuth?providers=`X` &postHooks=`hookNewsletter`]]
  Snippet args: user, userid, provider, profile, ha_mode (register|login).
  Events: OnHAUserCreate, OnHAUserLogin, OnHAUserBind.

Security:
- Register the exact HTTPS callback URI at each IdP.
- Do not host open-redirect pages on the same domain as site_url (Covert Redirect / #25).
- &redirectUri= must be same-origin as site_url; other hosts are rejected.
- Hybridauth uses OAuth authorization code + state.

Init errors go to the MODX system log.

--------------------
Building from source
--------------------
vendor/ is not committed. build.transport.php runs `composer install --no-dev`
in core/components/hybridauth/ and aborts if vendor/autoload.php is missing.

Manual install:
  cd core/components/hybridauth && composer install --no-dev

composer.lock pins the Hybridauth version for builds.

MailRu is not shipped (removed upstream in Hybridauth 3.8.2). VK ID can cover Mail.ru accounts.

VK ID (OAuth 2.1 + PKCE):
1. Create an app at https://id.vk.com/about/business/go
2. Set ha.keys.VkId, e.g. {"keys":{"id":"...","secret":"..."},"scope":"vkid.personal_info email"}
3. Callback: {site_url}?hauth_done=VkId
4. Snippet: [[!HybridAuth?providers=`VkId`]]

Legacy ha.keys.Vkontakte (api.vk.com) is for old apps only.
