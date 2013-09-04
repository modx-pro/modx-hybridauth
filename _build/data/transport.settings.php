<?php
$settings = array();

$tmp = array(
	'keys.Yandex' => array(
		'xtype' => 'textfield',
		'value' => '{"id":"12345","secret":"12345"}',
	),
	'keys.Twitter' => array(
		'xtype' => 'textfield',
		'value' => '{"key":"12345","secret":"12345"}',
	),
	'keys.Google' => array(
		'xtype' => 'textfield',
		'value' => '{"id":"12345","secret":"12345"}',
	),
	'register_users' => array(
		'xtype' => 'combo-boolean',
		'value' => true,
	),
);

foreach ($tmp as $k => $v) {
	/* @var modSystemSetting $setting */
	$setting = $modx->newObject('modSystemSetting');
	$setting->fromArray(array_merge(
		array(
			'key' => 'ha.'.$k,
			'namespace' => PKG_NAME_LOWER.':default',
			'area' => '',
		), $v
	),'',true,true);

	$settings[] = $setting;
}

unset($tmp);
return $settings;