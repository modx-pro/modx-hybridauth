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
	'profileTpl' => array(
		'type' => 'textfield',
		'value' => 'tpl.HybridAuth.profile',
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
	'profileFields' => array(
		'type' => 'textfield',
		'value' => 'username:25,email:50,fullname:50,phone:12,mobilephone:12,dob:10,gender,address,country,city,state,zip,fax,photo,comment,website',
	),
	'action' => array(
		'type' => 'list',
		'value' => 'loadTpl',
		'options' => array(
			array('text' => 'loadTpl','value' => 'loadTpl'),
			array('text' => 'getProfile','value' => 'getProfile'),
		),
	),
	'requiredFields' => array(
		'type' => 'textfield',
		'value' => 'username,email,fullname',
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