<?php
$modx->error->message = null;

$HybridAuth = $modx->getService('hybridauth','HybridAuth',$modx->getOption('hybridauth.core_path',null,$modx->getOption('core_path').'components/hybridauth/').'model/hybridauth/',$scriptProperties);
if (!($HybridAuth instanceof HybridAuth)) return '';

if ($modx->error->hasError()) {
	return $modx->error->message;
}

// If user sends action
if (!empty($_REQUEST['hauth_action'])) {
	// And he wants to update his profile - it will be handled only by snippet that called with action getProfile
	if ($_REQUEST['hauth_action'] == 'updateProfile' && $modx->getOption('action', $scriptProperties) == 'getProfile') {$action = 'updateProfile';}
}

if (empty($action)) {$action = $modx->getOption('action', $scriptProperties, 'loadTpl');}

$output = '';
switch ($action) {
	case 'getProfile': $output = $HybridAuth->getProfile(); break;
	case 'updateProfile': $output = $HybridAuth->updateProfile($_POST); break;
	case 'loadTpl':
	default: $output = $HybridAuth->loadTpl(); break;
}

return $output;