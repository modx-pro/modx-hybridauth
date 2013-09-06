<?php
/**
 * Properties Ukrainian Lexicon Entries for HybridAuth
 *
 * @package hybridauth
 * @subpackage lexicon
 * @translation by Viktorminator
 */
$_lang['ha.providers'] = 'Список провайдерів авторизації, через кому. Усі доступні провайдери знаходяться тут {core_path}components/hybridauth/model/hybridauth/lib/Providers/. Наприклад, &providers=`Google,Twitter,Facebook`.';
$_lang['ha.groups'] = 'Список груп для реєстрації користувача, через кому. Можна вказувати роль юзера в групі через двокрапку. Наприклад, &groups=`Users:1` додасть юзера в групу "Users" з роллю "member".';
$_lang['ha.rememberme'] = 'Запам’ятовує користувача на тривалий час. Дефолтно - увімкнуто.';
$_lang['ha.loginContext'] = 'Основний контекст для авторизації. Дефолтно - поточний.';
$_lang['ha.addContexts'] = 'Додаткові контексти через кому. Наприклад, &addContexts=`web,ru,en`';

$_lang['ha.loginResourceId'] = 'Ідентифікатор ресурсу, на який відправляти юзера після авторизації. Дефолтно - 0 (оновлює поточну сторінку).';
$_lang['ha.logoutResourceId'] = 'ІдентифІкатор ресурсу, на який відправляти который отправлять юзера після завершення сесії. Дефолтно - 0 (оновлює поточну сторінку).';

$_lang['ha.loginTpl'] = 'Цей чанк буде показано анонімному користувачу, тобто будь-якому гостю.';
$_lang['ha.logoutTpl'] = 'Цей чанк буде показано авторизованому користувачу.';
$_lang['ha.profileTpl'] = 'Чанк для виводу і редагування профілю користувача.';
$_lang['ha.providerTpl'] = 'Чанк для виведення посилання на авторизацію або прив\'язку сервісу до облікового запису.';
$_lang['ha.activeProviderTpl'] = 'Чанк для виведення іконки прив\'язаного сервісу.';

$_lang['ha.profileFields'] = 'Список дозволених для редагування полів юзера, через кому. Також можна вказати максимальну довжину значень, через двокрапку. Наприклад, &profileFields=`username:25,fullname:50,email`.';
$_lang['ha.requiredFields'] = 'Список обов’язкових полів при редагуванні. Ці поля повинні бути заповнені для успішного оновлення профілю. Наприклад, &requiredFields=`username,fullname,email`.';
