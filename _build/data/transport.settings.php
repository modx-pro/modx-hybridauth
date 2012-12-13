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
	'key' => 'ha.keys.Example',
	'value' => '{"id":"195149345691.apps.googleusercontent.com","secret":"IjaE1KngN5PZE6srKjGllq4Z"}',
	'xtype' => 'textfield',
	'namespace' => 'hybridauth',
	'area' => '',
),'',true,true);

return $settings;