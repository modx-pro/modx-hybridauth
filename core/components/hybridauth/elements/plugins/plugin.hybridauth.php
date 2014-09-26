<?php
if (!$modx->loadClass('hybridauth', $modx->getOption('hybridauth.core_path', null, $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/hybridauth/') . 'model/hybridauth/', false, true)) {
	return;
}

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

	case 'OnUserFormPrerender':
		if (!isset($scriptProperties['user']) || $scriptProperties['user']->get('id') < 1) {
			return;
		}
		$HybridAuth = new HybridAuth($modx);
		$controller = $modx->controller;
		$controller->addLexiconTopic('hybridauth:default');
		$jsUrl = $HybridAuth->config['jsUrl'];
		$controller->addJavascript($jsUrl . 'mgr/hybridauth.js');
		$controller->addJavascript($jsUrl . 'mgr/service/grids.js');
		$modx->regClientStartupScript('<script type="text/javascript">
Ext.onReady(function() {
	HybridAuth.config = ' . $modx->toJSON($HybridAuth->config) . ';
	var tab = Ext.getCmp("modx-user-tabs");
	if (!tab) {
		return;
	}
	tab.add({
		title: _("ha.services"),
		items: [{
			layout: "anchor",
			border: false,
			items: [{
				html: _("ha.services_tip"),
				bodyCssClass: "panel-desc"
			}, {
				xtype: "hybridauth-grid-services",
				anchor: "100%",
				cls: "main-wrapper",
				userId: ' . intval($scriptProperties['user']->get('id')) . '
			}]
		}]
	});
});
		</script>');
		break;
}