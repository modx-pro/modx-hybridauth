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
	/*
	array(
		'name' => 'action'
		,'desc' => 'ha.action'
		,'type' => 'list'
		,'value' => 'getForm'
		,'options' => array(
			array('text' => 'loadTpl','value' => 'loadTpl')
			,array('text' => 'login','value' => 'login')
			,array('text' => 'logout','value' => 'logout')
		)
		,'lexicon' => 'hybridauth:properties'
	),
	*/
);

return $properties;