<?php
$xpdo_meta_map['haUser']= array (
  'package' => 'hybridauth',
  'version' => '1.0',
  'extends' => 'modUser',
  'fields' => 
  array (
  ),
  'fieldMeta' => 
  array (
  ),
  'composites' => 
  array (
    'Services' => 
    array (
      'local' => 'id',
      'class' => 'haUserService',
      'foreign' => 'internalKey',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
