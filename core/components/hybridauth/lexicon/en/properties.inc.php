<?php
/**
 * Properties English Lexicon Entries for HybridAuth
 *
 * @package hybridauth
 * @subpackage lexicon
 */
$_lang['ha.providers'] = 'Comma separated list of a providers for authentification. All available providers are here {core_path}components/hybridauth/model/hybridauth/lib/Providers/. For example, &providers=`Google,Twitter,Facebook`.';
$_lang['ha.groups'] = 'Comma separated list of existing user groups for joining by user at the first login. For example, &groups=`Users:1` will add new user to group "Users" with role "member"';
$_lang['ha.rememberme'] = 'If true, user will be remembered for a long time.';
$_lang['ha.loginContext'] = 'Main context for authentication. By default - it is current context.';
$_lang['ha.addContexts'] = 'Comma separated list of additional contexts for authentication. For example &addContexts=`web,ru,en`';

$_lang['ha.loginResourceId'] = 'Resource id to redirect to on successful login. By default, it is 0 - redirect to self.';
$_lang['ha.logoutResourceId'] = 'Resource id to redirect to on successful logout. By default, it is 0 - redirect to self.';

$_lang['ha.loginTpl'] = 'This chunk will see any anonymous user.';
$_lang['ha.logoutTpl'] = 'This chunk will see any authenticated user.';
$_lang['ha.profileTpl'] = 'Chunk for display and edit user profile.';
$_lang['ha.providerTpl'] = 'Chunk to display a link for authorization or linking a service to your account.';
$_lang['ha.activeProviderTpl'] = 'Chunk for output icon of linked service.';

$_lang['ha.profileFields'] = 'Comma separated list of allowed user fields for update with maximum length of sended values. For example, &profileFields=`username:25,fullname:50,email`.';
$_lang['ha.requiredFields'] = 'Comma separated list of required user fields when update. This fields must be filled for successful update of profile. For example, &requiredFields=`username,fullname,email`.';
$_lang['ha.redirectUri'] = 'You can specify "redirect_uri" to which remote service will redirect you. By default, this is the root of the current context.';