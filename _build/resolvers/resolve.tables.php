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
			$modelPath = $modx->getOption('hybridauth.core_path',null,$modx->getOption('core_path').'components/hybridauth/').'model/';
			$modx->addPackage('hybridauth',$modelPath);

			$manager = $modx->getManager();
			$manager->createObjectContainer('haUserService');
			$modx->addExtensionPackage('hybridauth', '[[++core_path]]components/hybridauth/model/');
			break;

		case xPDOTransport::ACTION_UPGRADE:
			break;

		case xPDOTransport::ACTION_UNINSTALL:
			$stmt = $modx->exec("UPDATE {$modx->getTableName('modUser')} SET `class_key` = 'modUser' WHERE `class_key` = 'haUser';");
			$modx->removeExtensionPackage('hybridauth');
			break;
	}
}
return true;