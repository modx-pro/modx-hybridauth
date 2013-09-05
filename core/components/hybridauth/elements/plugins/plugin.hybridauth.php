<?php
switch ($modx->event->name) {

	case 'OnHandleRequest':
		if ($modx->user->isAuthenticated()) {
			if (!$modx->user->active || $modx->user->Profile->blocked) {
				$modx->runProcessor('security/logout');
				$modx->sendRedirect($modx->makeUrl($modx->getOption('site_start'),'','','full'));
			}
		}

		if (!empty($_REQUEST['hauth_action']) || !empty($_REQUEST['hauth_start']) || !empty($_REQUEST['hauth_done'])) {
			
			if (!$modx->loadClass('hybridauth', MODX_CORE_PATH . 'components/hybridauth/model/hybridauth/', false, true)) {return;}
			$HybridAuth = new HybridAuth($modx);

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