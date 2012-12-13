<?php
/**
 * Add snippets to build
 * 
 * @package hybridauth
 * @subpackage build
 */
$snippets = array();

$snippets[0]= $modx->newObject('modSnippet');
$snippets[0]->fromArray(array(
	'id' => 0,
	'name' => 'HybridAuth',
	'description' => 'Social sign on',
	'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/hybridauth.php'),
),'',true,true);
$properties = include $sources['build'].'properties/hybridauth.php';
$snippets[0]->setProperties($properties);
unset($properties);

return $snippets;