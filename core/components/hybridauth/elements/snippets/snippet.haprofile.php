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
elseif (!$modx->user->isAuthenticated($modx->context->key)) {
	return $modx->lexicon('ha_err_not_logged_in');
}

if (empty($profileTpl)) {
	$profileTpl = 'tpl.HybridAuth.profile';
}
if (empty($profileFields)) {
	$profileFields = 'username:25,email:50,fullname:50,phone:12,mobilephone:12,dob:10,gender,address,country,city,state,zip,fax,photo,comment,website';
}
if (empty($requiredFields)) {
	$requiredFields = 'username,email,fullname';
}
if (empty($providerTpl)) {
	$providerTpl = 'tpl.HybridAuth.provider';
}
if (empty($activeProviderTpl)) {
	$activeProviderTpl = 'tpl.HybridAuth.provider.active';
}
$data = array();

// Update of profile
if ((!empty($_REQUEST['action']) && strtolower($_REQUEST['action']) == 'updateprofile') || (!empty($_REQUEST['hauth_action']) && strtolower($_REQUEST['hauth_action']) == 'updateprofile')) {
	$profileFields = array_map('trim', explode(',', $profileFields));
	foreach ($profileFields as $field) {
		if (strpos($field, ':') !== false) {
			list($key, $length) = explode(':', $field);
		}
		else {
			$key = $field;
			$length = 0;
		}

		if (isset($_REQUEST[$key])) {
			if ($key == 'comment') {
				$data[$key] = empty($length) ? $_REQUEST[$key] : substr($_REQUEST[$key], $length);
			}
			else {
				$data[$key] = $HybridAuth->Sanitize($_REQUEST[$key], $length);
			}
		}
	}

	$data['requiredFields'] = array_map('trim', explode(',', $requiredFields));

	/** @var modProcessorResponse $response */
	$response = $HybridAuth->runProcessor('web/user/update', $data);
	if ($response->isError()) {
		$data['error.message'] = $response->getMessage();
		foreach ($response->errors as $error) {
			$data['error.' . $error->field] = $error->message;
		}
	}
	$data['success'] = (integer)!$response->isError();
}

$add = array();
if ($services = $modx->user->getMany('Services')) {
	/* @var haUserService $service */
	foreach ($services as $service) {
		$add = array_merge($add, $service->toArray(strtolower($service->get('provider') . '.')));
	}
}

$url = $HybridAuth->getUrl();
$user = $modx->user->toArray();
$profile = $modx->user->Profile->toArray();
$data = array_merge(
	$user,
	$profile,
	$add,
	$data,
	array(
		'login_url' => $url . 'login',
		'logout_url' => $url . 'logout',
		'providers' => $HybridAuth->getProvidersLinks($providerTpl, $activeProviderTpl),
		'gravatar' => 'https://gravatar.com/avatar/' . md5(strtolower($profile['email'])),
	)
);

return $modx->getChunk($profileTpl, $data);