<?php
/**
 * Add chunks to build
 * 
 * @package hybridauth
 * @subpackage build
 */
$chunks = array();

$chunks[0]= $modx->newObject('modChunk');
$chunks[0]->fromArray(array(
	'id' => 0,
	'name' => 'tpl.HybridAuth.login',
	'description' => 'Chunk for guest',
	'snippet' => getSnippetContent($sources['source_core'].'/elements/chunks/login.tpl'),
),'',true,true);

$chunks[1]= $modx->newObject('modChunk');
$chunks[1]->fromArray(array(
	'id' => 0,
	'name' => 'tpl.HybridAuth.logout',
	'description' => 'Chunk for logged in',
	'snippet' => getSnippetContent($sources['source_core'].'/elements/chunks/logout.tpl'),
),'',true,true);

$chunks[2]= $modx->newObject('modChunk');
$chunks[2]->fromArray(array(
	'id' => 0,
	'name' => 'tpl.HybridAuth.profile',
	'description' => 'Chunk for profile update',
	'snippet' => getSnippetContent($sources['source_core'].'/elements/chunks/profile.tpl'),
),'',true,true);

return $chunks;