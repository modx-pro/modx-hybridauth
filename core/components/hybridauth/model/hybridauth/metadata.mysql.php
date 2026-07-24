<?php

$xpdo_meta_map = array(
    'xPDOSimpleObject' =>
        array(
            0 => 'haUserService',
        ),
);

// Guard: on PHP 8+ writing into a missing map key fatals the manager (#45, #46).
if (isset($this->map['modUser']) && is_array($this->map['modUser'])) {
    if (!isset($this->map['modUser']['composites']) || !is_array($this->map['modUser']['composites'])) {
        $this->map['modUser']['composites'] = array();
    }
    $this->map['modUser']['composites']['Services'] = array(
        'class' => 'haUserService',
        'local' => 'id',
        'foreign' => 'internalKey',
        'cardinality' => 'many',
        'owner' => 'local',
    );
}
