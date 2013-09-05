<?php
switch ($modx->event->name) {

	case 'OnHandleRequest':
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