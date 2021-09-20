<?php

require_once dirname(__FILE__, 4) . '/config.core.php';
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';

/** @var modX $modx */
$corePath = $modx->getOption('hybridauth.core_path', null, $modx->getOption('core_path') . 'components/hybridauth/');
require_once $corePath . 'model/hybridauth/hybridauth.class.php';
$HybridAuth = new HybridAuth($modx);

/** @var modConnectorRequest $request */
$request = $modx->request;
$request->handleRequest(array(
    'processors_path' => $HybridAuth->config['processorsPath'],
    'location' => '',
));
