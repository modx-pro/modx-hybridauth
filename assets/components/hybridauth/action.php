<?php
define('MODX_API_MODE', true);
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/index.php';

$modx->getService('error','error.modError');
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('FILE');
$modx->error->message = null;

$HybridAuth = $modx->getService('hybridauth','HybridAuth',$modx->getOption('hybridauth.core_path',null,$modx->getOption('core_path').'components/hybridauth/').'model/hybridauth/',array());
if (!($HybridAuth instanceof HybridAuth)) return '';

if ($modx->error->hasError()) {
	return $modx->error->message;
}

$HybridAuth->process();