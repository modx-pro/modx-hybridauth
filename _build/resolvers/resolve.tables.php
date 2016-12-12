<?php

if ($object->xpdo) {
    /** @var modX $modx */
    /** @var array $options */
    $modx =& $object->xpdo;

    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            $modelPath = MODX_CORE_PATH . 'components/hybridauth/model/';
            $modx->addPackage('hybridauth', $modelPath);

            $manager = $modx->getManager();
            $manager->createObjectContainer('haUserService');
            $modx->addExtensionPackage('hybridauth', '[[++core_path]]components/hybridauth/model/');

            $modx->exec(
                "UPDATE {$modx->getTableName('modUser')} SET `class_key` = 'modUser' WHERE `class_key` = 'haUser';"
            );
            break;

        case xPDOTransport::ACTION_UNINSTALL:
            break;
    }
}
return true;