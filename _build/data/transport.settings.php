<?php
$settings = array();

$tmp = array(
    'keys.Yandex' => array(
        'xtype' => 'textfield',
        'value' => '{"keys":{"id":"12345","secret":"12345"}}',
        'area' => 'ha.keys',
    ),
    'keys.GitHub' => array(
        'xtype' => 'textfield',
        'value' => '{"keys":{"id":"12345","secret":"12345"},"scope":"user:email"}',
        'area' => 'ha.keys',
    ),
    'keys.Google' => array(
        'xtype' => 'textfield',
        'value' => '{"keys":{"id":"12345","secret":"12345"},"scope":"profile https://www.googleapis.com/auth/plus.profile.emails.read"}',
        'area' => 'ha.keys',
    ),
    'keys.Facebook' => array(
        'xtype' => 'textfield',
        'value' => '{"keys":{"id":"12345","secret":"12345"},"scope":"email,public_profile"}',
        'area' => 'ha.keys',
    ),
    'keys.Vkontakte' => array(
        'xtype' => 'textfield',
        'value' => '{"keys":{"id":"12345","secret":"12345"},"scope":"email"}',
        'area' => 'ha.keys',
    ),
    'register_users' => array(
        'xtype' => 'combo-boolean',
        'value' => true,
        'area' => 'ha.main',
    ),
    'frontend_css' => array(
        'xtype' => 'textfield',
        'value' => '[[+assetsUrl]]css/web/default.css',
        'area' => 'ha.main',
    ),

);

foreach ($tmp as $k => $v) {
    /** @var modSystemSetting $setting */
    $setting = $modx->newObject('modSystemSetting');
    $setting->fromArray(array_merge(
        array(
            'key' => 'ha.' . $k,
            'namespace' => PKG_NAME_LOWER,
        ), $v
    ), '', true, true);

    $settings[] = $setting;
}

unset($tmp);
return $settings;
