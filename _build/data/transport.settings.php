<?php
/**
 * Loads system settings into build
 *
 * @package hybridauth
 * @subpackage build
 */
$settings = array();

$settings[0]= $modx->newObject('modSystemSetting');
$settings[0]->fromArray(array(
	'key' => 'ha.keys.Yandex',
	'value' => '{"id":"12345","secret":"12345"}',
	'xtype' => 'textfield',
	'namespace' => 'hybridauth',
	'area' => '',
),'',true,true);

$settings[1]= $modx->newObject('modSystemSetting');
$settings[1]->fromArray(array(
	'key' => 'ha.keys.Twitter',
	'value' => '{"key":"12345","secret":"12345"}',
	'xtype' => 'textfield',
	'namespace' => 'hybridauth',
	'area' => '',
),'',true,true);

$settings[2]= $modx->newObject('modSystemSetting');
$settings[2]->fromArray(array(
	'key' => 'ha.keys.Google',
	'value' => '{"id":"12345","secret":"12345"}',
	'xtype' => 'textfield',
	'namespace' => 'hybridauth',
	'area' => '',
),'',true,true);

return $settings;