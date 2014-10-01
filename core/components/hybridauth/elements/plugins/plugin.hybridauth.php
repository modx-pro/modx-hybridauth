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
				$modx->sendRedirect($modx->makeUrl($modx->getOption('site_start'), '', '', 'full'));
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

	case 'OnWebAuthentication':
		$modx->event->_output = !empty($_SESSION['HybridAuth']['verified']);
		unset($_SESSION['HybridAuth']['verified']);
		break;

	case 'OnUserFormPrerender':
		/** @var modUser $user */
		if (!isset($user) || $user->get('id') < 1) {
			return;
		}
		$HybridAuth = new HybridAuth($modx);
		$modx->controller->addJavascript($HybridAuth->config['jsUrl'] . 'mgr/hybridauth.js');
		$modx->controller->addJavascript($HybridAuth->config['jsUrl'] . 'mgr/service/grids.js');
		$modx->controller->addLexiconTopic('hybridauth:default');

		if ($modx->getCount('modPlugin', array('name' => 'AjaxManager', 'disabled' => false))) {
			$modx->controller->addHtml('
			<script type="text/javascript">
				HybridAuth.config = ' . $modx->toJSON($HybridAuth->config) . ';
				Ext.onReady(function() {
					window.setTimeout(function() {
						var tab = Ext.getCmp("modx-user-tabs");
						if (!tab) {return;}
						tab.add({
							title: _("ha.services"),
							border: false,
							items: [{
								layout: "anchor",
								border: false,
								items: [{
									html: _("ha.services_tip"),
									border: false,
									bodyCssClass: "panel-desc"
								}, {
									xtype: "hybridauth-grid-services",
									anchor: "100%",
									cls: "main-wrapper",
									userId: ' . intval($user->get('id')) . '
								}]
							}]
						});
					}, 10);
				});
			</script>'
			);
		}
		else {
			$modx->controller->addHtml('
			<script type="text/javascript">
				HybridAuth.config = ' . $modx->toJSON($HybridAuth->config) . ';
				Ext.ComponentMgr.onAvailable("modx-user-tabs", function() {
					this.on("beforerender", function() {
						this.add({
							title: _("ha.services"),
							border: false,
							items: [{
								layout: "anchor",
								border: false,
								items: [{
									html: _("ha.services_tip"),
									border: false,
									bodyCssClass: "panel-desc"
								}, {
									xtype: "hybridauth-grid-services",
									anchor: "100%",
									cls: "main-wrapper",
									userId: ' . intval($user->get('id')) . '
								}]
							}]
						});
					});
				});
			</script>'
			);
		}
		break;
}