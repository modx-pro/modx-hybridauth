<?php

/**
 * Default Russian Lexicon Entries for HybridAuth
 *
 * @package hybridauth
 * @subpackage lexicon
 */

$_lang['hybridauth'] = 'HybridAuth';
$_lang['ha_err_no_providers'] = 'Вы должны указать хотя бы одного провайдера.';
$_lang['ha_err_no_provider_keys'] = 'Не могу получить ключи [[+provider]] из системных настроек. [[+provider]] не был активирован.';
$_lang['ha_err_not_logged_in'] = 'Вы должны быть авторизованы для редактирования своего профиля.';
$_lang['ha_register_disabled'] = 'Регистрация новых пользователей отключена.';

$_lang['ha.username'] = 'Логин';
$_lang['ha.fullname'] = 'Имя пользователя';
$_lang['ha.email'] = 'Email';
$_lang['ha.updateProfile'] = 'Обновить профиль';
$_lang['ha.profile_update_success'] = 'Профиль успешно обновлен';
$_lang['ha.profile_update_error'] = 'Ошибка при обновлении профиля';
$_lang['ha.logout'] = 'Выйти &rarr;';
$_lang['ha.save_profile'] = 'Сохранить профиль';
$_lang['ha.greeting'] = 'Вы авторизованы как ';
$_lang['ha.providers_available'] = 'Доступные провайдеры ';
$_lang['ha.login_intro'] = 'Вы можете авторизоваться на сайте через: ';
$_lang['ha.gravatar'] = 'Аватар';
$_lang['ha.gravatar_desc'] = 'Картинка загружается с <a href="http://gravatar.com/" target="_blank">Gravatar</a>';

$_lang['ha.services'] = 'Провайдеры авторизации';
$_lang['ha.services_tip'] = 'Здесь вы можете видеть, какие провайдеры используются для авторизации пользователя на сайте.';
$_lang['ha.service_avatar'] = 'Аватарка';
$_lang['ha.service_createdon'] = 'Дата создания';
$_lang['ha.service_provider'] = 'Провайдер';
$_lang['ha.service_identifier'] = 'Идентификатор';
$_lang['ha.service_displayname'] = 'Имя';
$_lang['ha.service_email'] = 'Email';

$_lang['ha.service_remove'] = 'Отвязать провайдера';
$_lang['ha.services_remove'] = 'Отвязать провайдеров';
$_lang['ha.service_remove_confirm'] = 'Вы уверены, что хотите отвязать этого провайдера от профиля пользователя?';
$_lang['ha.services_remove_confirm'] = 'Вы уверены, что хотите отвязать этих провайдеров от профиля пользователя?';

$_lang['area_ha.main'] = 'Основные';
$_lang['area_ha.keys'] = 'Ключи';
$_lang['setting_ha.keys.Google'] = 'Ключи для Google';
$_lang['setting_ha.keys.Google_desc'] = 'Добавить приложение и сгенерировать ключи можно по этой ссылке: <a target="_blank" href="https://code.google.com/apis/console/">https://code.google.com/apis/console/</a>.';
$_lang['setting_ha.keys.GitHub'] = 'Ключи для GitHub';
$_lang['setting_ha.keys.GitHub_desc'] = 'Добавить приложение и сгенерировать ключи можно по этой ссылке: <a target="_blank" href="https://github.com/settings/applications/new">https://github.com/settings/applications/new</a>.';
$_lang['setting_ha.keys.Twitter'] = 'Ключи для Twitter';
$_lang['setting_ha.keys.Twitter_desc'] = 'OAuth 1.0a приложение: <a target="_blank" href="https://developer.x.com/">developer.x.com</a>. В JSON нужен consumer <code>key</code> (не только id): {"keys":{"key":"...","secret":"..."}}. Callback: {site_url}?hauth_done=Twitter. Для OAuth 2 используйте провайдер X.';
$_lang['setting_ha.keys.Yandex'] = 'Ключи для Yandex';
$_lang['setting_ha.keys.Yandex_desc'] = 'Создайте приложение и получите Client ID / Client secret на <a target="_blank" href="https://oauth.yandex.ru/">https://oauth.yandex.ru/</a>. Документация: <a target="_blank" href="https://yandex.ru/dev/id/doc/ru/">Yandex ID OAuth</a>.';
$_lang['setting_ha.keys.Facebook'] = 'Ключи для Facebook';
$_lang['setting_ha.keys.Facebook_desc'] = 'Приложение: <a target="_blank" href="https://developers.facebook.com/apps">developers.facebook.com/apps</a>. Valid OAuth redirect URI — HTTPS и с подчёркиванием: {site_url}?hauth_done=Facebook (не hauth.done). JSON: {"keys":{"id":"...","secret":"..."},"scope":"email,public_profile"}.';
$_lang['setting_ha.keys.Vkontakte'] = 'Ключи для Vkontakte';
$_lang['setting_ha.keys.Vkontakte_desc'] = 'Legacy OAuth ВКонтакте: App ID и защищённый ключ. Новые приложения — в кабинете <a target="_blank" href="https://dev.vk.com/ru/vkid">VK ID</a> (отдельный адаптер: issue #56). Пример: {"keys":{"id":"...","secret":"..."},"scope":"email"}.';
$_lang['setting_ha.keys.Odnoklassniki'] = 'Ключи для Odnoklassniki';
$_lang['setting_ha.keys.Odnoklassniki_desc'] = 'Создайте приложение с платформой OAuth: <a target="_blank" href="https://apiok.ru/dev/app/create">apiok.ru</a> (права разработчика: <a target="_blank" href="https://ok.ru/devaccess">ok.ru/devaccess</a>). JSON: {"keys":{"id":"...","key":"...","secret":"..."}}, где key — публичный application_key. В redirect_uri добавьте {site_url}?hauth_done=Odnoklassniki.';

$_lang['setting_ha.frontend_css'] = 'Стили фронтенда';
$_lang['setting_ha.frontend_css_desc'] = 'Путь к файлу со стилями компонента. Если вы хотите использовать собственные стили - укажите путь к ним здесь, или очистите параметр и загрузите их вручную через шаблон сайта.';
$_lang['setting_ha.register_users'] = 'Регистрировать новых';
$_lang['setting_ha.register_users_desc'] = 'Эта настройка определяет, нужно ли создавать новых пользователей при авторизации через HybridAuth.';
