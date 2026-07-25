<?php

$xpdo_meta_map = array(
    'xPDOSimpleObject' =>
        array(
            0 => 'haUserService',
        ),
);

// Do not mutate $this->map at runtime here.
// On MODX 3 + xPDOMap (ArrayAccess), nested map writes can emit
// "Indirect modification of overloaded element ... has no effect".
// Services are loaded explicitly via haUserService queries in snippets.
