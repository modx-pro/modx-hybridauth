<?php
/**
 * Resolve creating db tables
 *
 * @package hybridauth
 * @subpackage build
 */
if ($object->xpdo) {
	/* @var $modx modX */
	$modx =& $object->xpdo;

	switch ($options[xPDOTransport::PACKAGE_ACTION]) {
		case xPDOTransport::ACTION_INSTALL:
		case xPDOTransport::ACTION_UPGRADE:
			$modelPath = $modx->getOption('hybridauth.core_path', null, $modx->getOption('core_path') . 'components/hybridauth/') . 'model/';
			$modx->addPackage('hybridauth', $modelPath);

			$manager = $modx->getManager();
			$manager->createObjectContainer('haUserService');
			$modx->addExtensionPackage('hybridauth', '[[++core_path]]components/hybridauth/model/');

			$modx->exec("UPDATE {$modx->getTableName('modUser')} SET `class_key` = 'modUser' WHERE `class_key` = 'haUser';");
			break;

		case xPDOTransport::ACTION_UNINSTALL:
			break;
	}
}
return true;