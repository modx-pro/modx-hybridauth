<?php
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';

$corePath = $modx->getOption('hybridauth.core_path', null, $modx->getOption('core_path') . 'components/hybridauth/');
require_once $corePath . 'model/hybridauth/hybridauth.class.php';
$HybridAuth = new HybridAuth($modx);

$modx->request->handleRequest(array(
	'processors_path' => $HybridAuth->config['processorsPath'],
	'location' => '',
));
