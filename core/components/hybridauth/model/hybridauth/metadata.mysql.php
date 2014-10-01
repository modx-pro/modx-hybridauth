<?php

$xpdo_meta_map = array(
	'xPDOSimpleObject' =>
		array(
			0 => 'haUserService',
		),
);


$this->map['modUser']['composites']['Services'] = array(
	'class' => 'haUserService',
	'local' => 'id',
	'foreign' => 'internalKey',
	'cardinality' => 'many',
	'owner' => 'local',
);