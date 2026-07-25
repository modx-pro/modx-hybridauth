<?php

$events = array();

$tmp = array(
    'OnHAUserCreate' => array(),
    'OnHAUserLogin' => array(),
    'OnHAUserBind' => array(),
);

/** @var modX $modx */
foreach ($tmp as $k => $v) {
    /** @var modEvent $event */
    $event = $modx->newObject('modEvent');
    $event->fromArray(array_merge(array(
        'name' => $k,
        'service' => 6,
        'groupname' => PKG_NAME,
    ), $v), '', true, true);
    $events[] = $event;
}

return $events;
