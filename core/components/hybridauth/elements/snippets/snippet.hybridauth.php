<?php
$modx->error->message = null;

$HybridAuth = $modx->getService('hybridauth','HybridAuth',$modx->getOption('hybridauth.core_path',null,$modx->getOption('core_path').'components/hybridauth/').'model/hybridauth/',$scriptProperties);
if (!($HybridAuth instanceof HybridAuth)) return '';

if ($modx->error->hasError()) {
	return $modx->error->message;
}

// If user sends action
if (!empty($_REQUEST['action'])) {
	// And it is login or logout - it will override any action
	if (in_array($_REQUEST['action'], array('login','logout'))) {$action = $_REQUEST['action'];}
	// And he wants to update his profile - it will be handled only by snippet that called with action getProfile
	else if ($_REQUEST['action'] == 'updateProfile' && $modx->getOption('action', $scriptProperties) == 'getProfile') {$action = 'updateProfile';}
}

if (empty($action)) {$action = $modx->getOption('action', $scriptProperties, 'loadTpl');}

$output = '';
switch ($action) {
	case 'login': $output = $HybridAuth->Login(@$_REQUEST['provider']); break;
	case 'logout': $HybridAuth->Logout(); break;
	case 'getProfile': return $HybridAuth->getProfile(); break;
	case 'updateProfile': return $HybridAuth->updateProfile($_POST); break;
	case 'loadTpl':
	default: $output =  $HybridAuth->loadTpl(); break;
}

return $output;