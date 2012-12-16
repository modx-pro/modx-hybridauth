<?php
/**
 * Properties for the HybridAuth snippet.
 *
 * @package hybridauth
 * @subpackage build
 */
$properties = array(
	array(
		'name' => 'providers',
		'desc' => 'ha.providers',
		'type' => 'textfield',
		'value' => '',
		'lexicon' => 'hybridauth:properties',
	),
	array(
		'name' => 'rememberme',
		'value' => true,
		'type' => 'combo-boolean',
		'desc' => 'ha.rememberme',
	),
	array(
		'name' => 'loginTpl',
		'value' => 'tpl.HybridAuth.login',
		'type' => 'textfield',
		'desc' => 'ha.loginTpl',
	),
	array(
		'name' => 'logoutTpl',
		'value' => 'tpl.HybridAuth.logout',
		'type' => 'textfield',
		'desc' => 'ha.logoutTpl',
	),
	array(
		'name' => 'profileTpl',
		'value' => 'tpl.HybridAuth.profile',
		'type' => 'textfield',
		'desc' => 'ha.profileTpl',
	),
	array(
		'name' => 'groups',
		'value' => '',
		'type' => 'textfield',
		'desc' => 'ha.groups',
	),
	array(
		'name' => 'loginContext',
		'value' => '',
		'type' => 'textfield',
		'desc' => 'ha.loginContext',
	),
	array(
		'name' => 'addContexts',
		'value' => '',
		'type' => 'textfield',
		'desc' => 'ha.addContexts',
	),
	array(
		'name' => 'profileFields',
		'value' => 'username:25,email:50,fullname:50,phone:12,mobilephone:12,dob:10,gender,address,country,city,state,zip,fax,photo,comment,website',
		'type' => 'textfield',
		'desc' => 'ha.profileFields',
	),
	array(
		'name' => 'action',
		'value' => 'loadTpl',
		'type' => 'list',
		'desc' => 'ha.action',
		'options' => array(
			array('text' => 'loadTpl','value' => 'loadTpl'),
			array('text' => 'getProfile','value' => 'getProfile'),
		),
	),
	array(
		'name' => 'requiredFields',
		'value' => 'username,email,fullname',
		'type' => 'textfield',
		'desc' => 'ha.requiredFields',
	),
	array(
		'name' => 'loginResourceId',
		'value' => 0,
		'type' => 'numberfield',
		'desc' => 'ha.loginResourceId',
	),
	array(
		'name' => 'logoutResourceId',
		'value' => 0,
		'type' => 'numberfield',
		'desc' => 'ha.logoutResourceId',
	),
);

return $properties;