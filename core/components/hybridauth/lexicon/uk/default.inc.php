<?php

/**
 * Default Ukrainian Lexicon Entries for HybridAuth
 *
 * @package hybridauth
 * @subpackage lexicon
 * @translation Viktorminator, dmi3yy <dmi3yy@gmail.com>
 */

$_lang['hybridauth'] = 'HybridAuth';
$_lang['ha_err_no_providers'] = 'Ви повинні вказати хоча б одного провайдера.';
$_lang['ha_err_not_logged_in'] = 'Ви повинні бути авторизовані для редагування свого профілю';
$_lang['ha_err_no_provider_keys'] = 'Не можу отримати ключі [[+provider]] з системних налаштувань. [[+provider]] не було активовано.';

$_lang['ha.username'] = 'Логін';
$_lang['ha.fullname'] = 'Ім’я користувача';
$_lang['ha.email'] = 'Email';
$_lang['ha.updateProfile'] = 'Оновити профіль';
$_lang['ha.profile_update_success'] = 'Профіль оновлено';
$_lang['ha.profile_update_error'] = 'Помилка при оновлені профілю';
$_lang['ha.logout'] = 'Вийти &rarr;';
$_lang['ha.save_profile'] = 'Зберегти профіль';
$_lang['ha.greeting'] = 'Ви авторизовані як ';
$_lang['ha.providers_available'] = 'Доступні провайдери ';
$_lang['ha.login_intro'] = 'Ви можете авторизуватись на сайті за допомоги: ';
$_lang['ha.gravatar'] = 'Аватар';
$_lang['ha.gravatar_desc'] = 'Картинка завантажується з <a href="http://gravatar.com/" target="_blank">Gravatar</a>';

$_lang['area_ha.main'] = 'Основні';
$_lang['area_ha.keys'] = 'Ключі';
$_lang['setting_ha.keys.Google'] = 'Ключі для Google';
$_lang['setting_ha.keys.Google_desc'] = 'Додати додаток та згенерувати ключі можна за цим посиланням: <a target="_blank" href="https://code.google.com/apis/console/">https://code.google.com/apis/console/</a>. Про налаштування інших провайдерів можна прочитати в документації: <a target="_blank" href="http://hybridauth.sourceforge.net/userguide.html">http://hybridauth.sourceforge.net/userguide.html</a>.';
$_lang['setting_ha.keys.Twitter'] = 'Ключі для Twitter';
$_lang['setting_ha.keys.Twitter_desc'] = 'OAuth 1.0a додаток: <a target="_blank" href="https://developer.x.com/">developer.x.com</a>. У JSON потрібен consumer <code>key</code> (не лише id): {"keys":{"key":"...","secret":"..."}}. Callback: {site_url}?hauth_done=Twitter. Для OAuth 2 використовуйте провайдер X.';
$_lang['setting_ha.keys.Yandex'] = 'Ключі для Yandex';
$_lang['setting_ha.keys.Yandex_desc'] = 'Створіть додаток і отримайте Client ID / Client secret на <a target="_blank" href="https://oauth.yandex.ru/">https://oauth.yandex.ru/</a>. Документація: <a target="_blank" href="https://yandex.com/dev/id/doc/en/oauth-cabinet">Yandex ID OAuth</a>.';
$_lang['setting_ha.keys.Facebook'] = 'Ключі для Facebook';
$_lang['setting_ha.keys.Facebook_desc'] = 'Додаток: <a target="_blank" href="https://developers.facebook.com/apps">developers.facebook.com/apps</a>. Valid OAuth redirect URI — HTTPS і з підкресленням: {site_url}?hauth_done=Facebook (не hauth.done). JSON: {"keys":{"id":"...","secret":"..."},"scope":"email,public_profile"}.';
$_lang['setting_ha.keys.Vkontakte'] = 'Ключі для Vkontakte';
$_lang['setting_ha.keys.Vkontakte_desc'] = 'Legacy OAuth ВКонтакті: App ID і захищений ключ. Нові додатки — в кабінеті <a target="_blank" href="https://dev.vk.com/ru/vkid">VK ID</a> (окремий адаптер: issue #56). Приклад: {"keys":{"id":"...","secret":"..."},"scope":"email"}.';
$_lang['setting_ha.keys.Odnoklassniki'] = 'Ключі для Odnoklassniki';
$_lang['setting_ha.keys.Odnoklassniki_desc'] = 'Створіть додаток з платформою OAuth: <a target="_blank" href="https://apiok.ru/dev/app/create">apiok.ru</a> (права розробника: <a target="_blank" href="https://ok.ru/devaccess">ok.ru/devaccess</a>). JSON: {"keys":{"id":"...","key":"...","secret":"..."}}, де key — публічний application_key. У redirect_uri додайте {site_url}?hauth_done=Odnoklassniki.';

$_lang['setting_ha.frontend_css'] = 'Стилі фронтенда';
$_lang['setting_ha.frontend_css_desc'] = 'Шлях до файлу зі стилями компонента. Якщо ви хочете використовувати власні стилі - вкажіть шлях до них тут, або очистіть параметр і завантажте їх вручну через шаблон сайту.';
$_lang['setting_ha.register_users'] = 'Реєструвати нових';
$_lang['setting_ha.register_users_desc'] = 'Цей параметр визначає, чи потрібно створювати нових користувачів при авторизації через HybridAuth.';
