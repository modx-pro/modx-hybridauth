<?php
switch ($modx->event->name) {

	case 'OnHandleRequest':
		if ($modx->context->key != 'web' && !$modx->user->id) {
			if ($user = $modx->getAuthenticatedUser($modx->context->key)) {
				$modx->user = $user;
				$modx->getUser($modx->context->key);
			}
		}

		if ($modx->user->isAuthenticated($modx->context->key)) {
			if (!$modx->user->active || $modx->user->Profile->blocked) {
				$modx->runProcessor('security/logout');
				$modx->sendRedirect($modx->makeUrl($modx->getOption('site_start'),'','','full'));
			}
		}

		if (!empty($_REQUEST['hauth_action']) || !empty($_REQUEST['hauth_start']) || !empty($_REQUEST['hauth_done'])) {
			
			if (!$modx->loadClass('hybridauth', MODX_CORE_PATH . 'components/hybridauth/model/hybridauth/', false, true)) {return;}
			$config = !empty($_SESSION['HybridAuth'][$modx->context->key])
				? $_SESSION['HybridAuth'][$modx->context->key]
				: array();
			$HybridAuth = new HybridAuth($modx, $config);

			if (!empty($_REQUEST['hauth_action'])) {
				switch ($_REQUEST['hauth_action']) {
					case 'login':
						$HybridAuth->Login(@$_REQUEST['provider']);
						break;
					case 'logout':
						$HybridAuth->Logout();
						break;
				}
			}
			else {
				$HybridAuth->processAuth();
			}
		}
		break;

}