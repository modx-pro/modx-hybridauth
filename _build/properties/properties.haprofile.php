<?php
$properties = array();

$tmp = array(
	'profileTpl' => array(
		'type' => 'textfield',
		'value' => 'tpl.HybridAuth.profile',
	),
	'profileFields' => array(
		'type' => 'textfield',
		'value' => 'username:25,email:50,fullname:50,phone:12,mobilephone:12,dob:10,gender,address,country,city,state,zip,fax,photo,comment,website',
	),
	'requiredFields' => array(
		'type' => 'textfield',
		'value' => 'username,email,fullname',
	),
	'providerTpl' => array(
		'type' => 'textfield',
		'value' => 'tpl.HybridAuth.provider',
	),
	'activeProviderTpl' => array(
		'type' => 'textfield',
		'value' => 'tpl.HybridAuth.provider.active',
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