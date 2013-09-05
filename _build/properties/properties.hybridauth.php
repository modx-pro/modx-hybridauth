<?php
$properties = array();

$tmp = array(
	'providers' => array(
		'type' => 'textfield',
		'value' => '',
	),
	'rememberme' => array(
		'type' => 'combo-boolean',
		'value' => true,
	),
	'loginTpl' => array(
		'type' => 'textfield',
		'value' => 'tpl.HybridAuth.login',
	),
	'logoutTpl' => array(
		'type' => 'textfield',
		'value' => 'tpl.HybridAuth.logout',
	),
	'groups' => array(
		'type' => 'textfield',
		'value' => '',
	),
	'loginContext' => array(
		'type' => 'textfield',
		'value' => '',
	),
	'addContexts' => array(
		'type' => 'textfield',
		'value' => '',
	),
	'loginResourceId' => array(
		'type' => 'numberfield',
		'value' => 0,
	),
	'logoutResourceId' => array(
		'type' => 'numberfield',
		'value' => 0,
	),
);

foreach ($tmp as $k => $v) {
	$properties[] = array_merge(array(
			'name' => $k,
			'desc' => 'ha.'.$k,
			'lexicon' => PKG_NAME_LOWER.':properties',
		), $v
	);
}

return $properties;