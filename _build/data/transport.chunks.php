<?php
$chunks = array();

$tmp = array(
	'tpl.HybridAuth.login' => array(
		'file' => 'login',
		'description' => 'Chunk for guest',
	),
	'tpl.HybridAuth.logout' => array(
		'file' => 'logout',
		'description' => 'Chunk for logged in',
	),
	'tpl.HybridAuth.profile' => array(
		'file' => 'profile',
		'description' => 'Chunk for profile update',
	),
);

foreach ($tmp as $k => $v) {
	/* @avr modChunk $chunk */
	$chunk = $modx->newObject('modChunk');
	$chunk->fromArray(array(
		'id' => 0,
		'name' => $k,
		'description' => @$v['description'],
		'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/chunk.'.$v['file'].'.tpl'),
		'static' => BUILD_CHUNK_STATIC,
		'source' => 1,
		'static_file' => 'core/components/'.PKG_NAME_LOWER.'/elements/chunks/chunk.'.$v['file'].'.tpl',
	),'',true,true);

	$chunks[] = $chunk;
}

unset($tmp);
return $chunks;