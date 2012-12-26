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

			if ($modx instanceof modX) {
				$modx->addExtensionPackage('hybridauth', '[[++core_path]]components/hybridauth/model/');
			}

		break;
		case xPDOTransport::ACTION_UPGRADE:
		break;
		case xPDOTransport::ACTION_UNINSTALL:
			$sql = "UPDATE {$modx->getTableName('modUser')} SET `class_key` = 'modUser' WHERE `class_key` = 'haUser';";
			/* @var PDOStatement $stmt */
			if ($stmt = $modx->prepare($sql)) {$stmt->execute();}
		break;
	}
}
return true;