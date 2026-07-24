--------------------
HybridAuth
--------------------
Author: Vasiliy Naumkin <bezumkin@yandex.ru>
--------------------
An integration of open source social sign on php library Hybridauth
(https://github.com/hybridauth/hybridauth, currently 3.13) into MODX Revolution.

Supported PHP: 7.4–8.4

The main goal of HybridAuth library is to act as an abstract api between your application and various social apis and identities providers such as Facebook, X, LinkedIn, Google and Yahoo.

HybridAuth enable developers to easily build social applications to engage websites vistors and customers on a social level by implementing social signin, social sharing, users profiles, friends list, activities stream, status updates and more.

Feel free to suggest ideas/improvements/bugs on GitHub:
https://github.com/modx-pro/modx-hybridauth/issues

--------------------
Installation
--------------------
Download and install it with MODX package manager

Then:
1. Register and get api keys from needed services. For example, create an X app - https://developer.x.com/
2. Open system settings in manager, switch to hybridauth and make\update ha.keys.Servicename. In our example it will be ha.keys.X
3. You need to set your keys as json string with array. Example:
   {"keys":{"id":"your id","secret":"your secret"}}
   X (OAuth 2) example with scope:
   {"keys":{"id":"...","secret":"..."},"scope":"tweet.read users.read users.email offline.access"}
   Legacy Twitter OAuth 1.0a uses consumer key (not id):
   {"keys":{"key":"...","secret":"..."}}
   Yahoo example (enable OpenID Connect Permissions in the Yahoo app):
   {"keys":{"id":"...","secret":"..."},"scope":"profile"}
   Odnoklassniki also needs public application_key as "key":
   {"keys":{"id":"...","key":"...","secret":"..."}}
   Add the same callback URL in the provider cabinet: {site_url}?hauth_done={Provider}
   (underscore; Facebook rejects hauth.done). site_url must be https on TLS sites.
4. Now you can run snippet [[!HybridAuth?providers=`X`]] or [[!HybridAuth?providers=`Yahoo`]] on any page.

Post hooks (issue #31):
  [[!HybridAuth?providers=`X` &postHooks=`hookNewsletter`]]
  Snippet gets user, userid, provider, profile, ha_mode (register|login).
  System events: OnHAUserCreate, OnHAUserLogin, OnHAUserBind.

Security:
- Register the exact HTTPS callback URI in each IdP. Do not put open-redirect pages on the
  same domain as site_url (Covert Redirect / issue #25).
- Snippet &redirectUri= must be same-origin as site_url; other hosts are rejected.
- Hybridauth uses OAuth authorization code + state.

If there will be any errors on library initialization - it will be logged in in system log.

--------------------
Building from source
--------------------
vendor/ is not committed. build.transport.php runs `composer install --no-dev`
in core/components/hybridauth/ and aborts if vendor/autoload.php is still missing.

You can also install dependencies yourself:
  cd core/components/hybridauth && composer install --no-dev

composer.lock is committed so builds resolve the same Hybridauth version.

MailRu is not shipped (removed upstream in Hybridauth 3.8.2); VK ID can cover Mail.ru accounts.

VK ID (OAuth 2.1 + PKCE):
1. Create an app at https://id.vk.com/about/business/go
2. Set ha.keys.VkId, e.g. {"keys":{"id":"...","secret":"..."},"scope":"vkid.personal_info email"}
3. Callback {site_url}?hauth_done=VkId
4. Snippet [[!HybridAuth?providers=`VkId`]]
Legacy ha.keys.Vkontakte (api.vk.com) remains for old apps only.
