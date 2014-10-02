<?php
/** @var array $scriptProperties */

$modx->error->message = null;
if (!$modx->loadClass('hybridauth', MODX_CORE_PATH . 'components/hybridauth/model/hybridauth/', false, true)) {
	return;
}
$HybridAuth = new HybridAuth($modx, $scriptProperties);
$HybridAuth->initialize($modx->context->key);

if ($modx->error->hasError()) {
	return $modx->error->message;
}
// For compatibility with old snippet
elseif (!empty($action)) {
	$tmp = strtolower($action);
	if ($tmp == 'getprofile' || $tmp == 'updateprofile') {
		return $modx->runSnippet('haProfile', $scriptProperties);
	}
}

if (empty($loginTpl)) {
	$loginTpl = 'tpl.HybridAuth.login';
}
if (empty($logoutTpl)) {
	$logoutTpl = 'tpl.HybridAuth.logout';
}
if (empty($providerTpl)) {
	$providerTpl = 'tpl.HybridAuth.provider';
}
if (empty($activeProviderTpl)) {
	$activeProviderTpl = 'tpl.HybridAuth.provider.active';
}

$url = $HybridAuth->getUrl();
$error = '';
if (!empty($_SESSION['HA']['error'])) {
	$error = $_SESSION['HA']['error'];
	unset($_SESSION['HA']['error']);
}

if ($modx->user->isAuthenticated($modx->context->key)) {
	$add = array();
	if ($services = $modx->user->getMany('Services')) {
		/* @var haUserService $service */
		foreach ($services as $service) {
			$add = array_merge($add, $service->toArray(strtolower($service->get('provider') . '.')));
		}
	}

	$user = $modx->user->toArray();
	$profile = $modx->user->Profile->toArray();
	unset($profile['id']);
	$arr = array_merge(
		$user,
		$profile,
		$add,
		array(
			'login_url' => $url . 'login',
			'logout_url' => $url . 'logout',
			'providers' => $HybridAuth->getProvidersLinks($providerTpl, $activeProviderTpl),
			'error' => $error,
			'gravatar' => 'https://gravatar.com/avatar/' . md5(strtolower($profile['email'])),
		)
	);

	return $modx->getChunk($logoutTpl, $arr);
}
else {
	$arr = array(
		'login_url' => $url . 'login',
		'logout_url' => $url . 'logout',
		'providers' => $HybridAuth->getProvidersLinks($providerTpl, $activeProviderTpl),
		'error' => $error,
	);

	return $modx->getChunk($loginTpl, $arr);
}