<?php

class HybridAuth {

	/** @var Hybrid_Auth $Hybrid_Auth */
	var $Hybrid_Auth;
	/** @var HybridAuthControllerRequest $request */
	var $request;

	function __construct(modX &$modx,array $config = array()) {
		$this->modx =& $modx;

		set_exception_handler(array($this, 'exceptionHandler'));

		$corePath = $this->modx->getOption('hybridauth.core_path',$config,$this->modx->getOption('core_path').'components/hybridauth/');
		$this->modx->lexicon->load('hybridauth:default');
		$this->modx->lexicon->load('core:user');

		if (empty($config) && !empty($_SESSION['HybridAuth'])) {
			$this->config = $_SESSION['HybridAuth'];
		}
		else {
			$this->config = array_merge(array(
				'corePath' => $corePath,
				'modelPath' => $corePath.'model/',
				'processorsPath' => $corePath.'processors/',

				'rememberme' => true,
				'groups' => '',
				'loginContext' => '',
				'addContexts' => '',
				'loginResourceId' => 0,
				'logoutResourceId' => 0,
			),$config);

			$response = $this->loadHybridAuth();
			if ($response !== true) {
				$this->modx->error->failure('[HybridAuth] ' . $response);
			}
		}

		$this->modx->addPackage('hybridauth',$this->config['modelPath']);

		$_SESSION['HybridAuth'] = $this->config;
		if (!empty($this->config['HA'])) {
			require_once 'lib/Auth.php';
			$this->Hybrid_Auth = new Hybrid_Auth($this->config['HA']);
		}
	}


	/**
	 * Custom exception handler for Hybrid_Auth
	 *
	 * @param Exception $e Exception object
	 * @return void;
	 */
	public function exceptionHandler(Exception $e) {
		$code = $e->getCode();
		if ($code < 5) {$level = modX::LOG_LEVEL_ERROR;}
		else {$level = modX::LOG_LEVEL_INFO;}

		$this->modx->log($level, '[HybridAuth] ' . $e->getMessage());
		$this->Refresh();
	}


	/**
	 * Loads settings for Hybrid_Auth class
	 *
	 * @return bool|null|string
	 */
	public function loadHybridAuth() {
		$keys = array();
		$tmp = array_map('trim', explode(',', $this->config['providers']));
		foreach ($tmp as $v) {
			if (!empty($v)) {
				$keys[] = 'ha.keys.'.$v;
			}
		}

		$providers = array();
		$q = $this->modx->newQuery('modSystemSetting');
		$q->select('key,value');
		$condition = !empty($keys)
			? array('key:IN' => $keys)
			: array('key:LIKE' => 'ha.keys.%');
		$q->where($condition);
		if ($q->prepare() && $q->stmt->execute()) {
			while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
				$providers[ucfirst(substr($row['key'],8))] = array(
					'enabled' => true,
					'keys' => $this->modx->fromJSON($row['value'])
				);
			}
		}

		if (empty($providers)) {
			return $this->modx->lexicon('ha_err_no_providers');
		}

		$this->config['HA'] = array(
			'base_url' => $this->modx->makeUrl($this->modx->getOption('site_start'), $this->modx->context->key, '', 'full'),
			'debug_mode' => !empty($config['debug']) && ($config['debug'] == 'true' || $config['debug'] == 1) ? 1 : 0,
			'debug_file' => MODX_CORE_PATH . 'cache/logs/error.log',
			'providers' => $providers,
		);



		return true;
	}

	/**
	 * Process Hybrid_Auth endpoint
	 *
	 * @return void
	 */
	function processAuth() {
		require 'lib/Endpoint.php';
		Hybrid_Endpoint::process();
	}


	/**
	 * Checks and login user. Also creates/updated user services profiles
	 *
	 * @param string $provider Remote service to login
	 * @return void
	 */
	function Login($provider = '') {
		$this->Hybrid_Auth->authenticate($provider);
		unset($_SESSION['HA']['error']);
		/* @var Hybrid_User_Profile $service */
		if ($profile = $this->getServiceProfile($provider)) {

			$profile['provider'] = $provider;

			// Checking for existing provider record in database
			/* @var haUserService $service */
			if (!$service = $this->modx->getObject('haUserService', array('identifier' => $profile['identifier'], 'provider' => $profile['provider']))) {
				// Adding new record to current user
				if ($this->modx->user->isAuthenticated()) {
					$uid = $this->modx->user->id;
					$profile['internalKey'] = $uid;

					// Changing class for existing user
					if ($this->modx->user->class_key != 'haUser') {
						$this->modx->user->set('class_key', 'haUser');
						$this->modx->user->save();
					}

					$response = $this->runProcessor('web/service/create', $profile);
					if ($response->isError()) {
						$this->modx->log(modX::LOG_LEVEL_ERROR, '[HybridAuth] unable to save service profile for user '.$uid.'. Message: '.implode(', ',$response->getAllErrors()));
						$_SESSION['HA']['error'] = implode(', ',$response->getAllErrors());
					}
				}
				// Creating new user and adding this record to him
				else {
					$username = !empty($profile['displayName']) ? $profile['displayName'] : $profile['identifier'];
					if ($exists = $this->modx->getCount('haUser', array('username' => $username))) {
						for ($i = 1; $i <= 10; $i++) {
							$tmp = $username . $i;
							if (!$this->modx->getCount('haUser', array('username' => $tmp))) {
								$username = $tmp;
								break;
							}
						}
					}
					$arr = array(
						'username' => $username
						,'fullname' => !empty($profile['lastName']) ? $profile['firstName'] .' '. $profile['lastName'] : $profile['firstName']
						,'dob' => !empty($profile['birthday']) && !empty($profile['birthmonth']) && !empty($profile['birthyear']) ? $profile['birthyear'].'-'.$profile['birthmonth'].'-'.$profile['birthday'] : ''
						,'email' => !empty($profile['emailVerified']) ? $profile['emailVerified'] : $profile['email']
						,'photo' => !empty($profile['photoURL']) ? $profile['photoURL'] : ''
						,'website' => !empty($profile['webSiteURL']) ? $profile['webSiteURL'] : ''
						,'phone' => !empty($profile['phone']) ? $profile['phone'] : ''
						,'address' => !empty($profile['address']) ? $profile['address'] : ''
						,'country' => !empty($profile['country']) ? $profile['country'] : ''
						,'state' => !empty($profile['region']) ? $profile['region'] : ''
						,'city' => !empty($profile['city']) ? $profile['city'] : ''
						,'zip' => !empty($profile['zip']) ? $profile['zip'] : ''
						,'active' => 1
						,'provider' => $profile
						,'groups' => $this->config['groups']
					);
					$response = $this->runProcessor('web/user/create', $arr);
					if ($response->isError()) {
						$this->modx->log(modX::LOG_LEVEL_ERROR, '[HybridAuth] Unable to create user '.print_r($arr,1).'. Message: '.implode(', ',$response->getAllErrors()));
						$_SESSION['HA']['error'] = implode(', ',$response->getAllErrors());
					}
					else {
						$login_data = array(
							'username' => $response->response['object']['username'],
							'password' => md5(rand()),
							'rememberme' => $this->config['rememberme']
						);
						$uid = $response->response['object']['id'];
						$profile['internalKey'] = $uid;
						$response = $this->runProcessor('web/service/create', $profile);
						if ($response->isError()) {
							$this->modx->log(modX::LOG_LEVEL_ERROR, '[HybridAuth] unable to save service profile for user '.$uid.'. Message: '.implode(', ',$response->getAllErrors()));
							$_SESSION['HA']['error'] = implode(', ',$response->getAllErrors());
						}
					}
				}
			}
			else {
				// Find and use linked MODX user
				if ($this->modx->user->isAuthenticated()) {
					$uid = $this->modx->user->id;
				}
				else {
					$uid = $service->get('internalKey');
				}

				/* @var haUser $user */
				if ($user = $this->modx->getObject('modUser', $uid)) {
					// Changing class for existing user
					if ($user->class_key != 'haUser') {
						$user->set('class_key', 'haUser');
						$user->save();
					}

					$login_data = array(
						'username' => $user->get('username'),
						'password' => md5(rand()),
						'rememberme' => $this->config['rememberme']
					);

					$profile['id'] = $service->get('id');
					$profile['internalKey'] = $uid;
					$response = $this->runProcessor('web/service/update', $profile);
					if ($response->isError()) {
						$this->modx->log(modX::LOG_LEVEL_ERROR, '[HybridAuth] unable to update service profile for user '.$uid.'. Message: '.implode(', ',$response->getAllErrors()));
						$_SESSION['HA']['error'] = implode(', ',$response->getAllErrors());
					}
				}
				else {
					$service->remove();
					$this->Login($provider);
					//$this->modx->log(modX::LOG_LEVEL_ERROR, '[HybridAuth] Could not find user with id = '.$uid);
					//$_SESSION['HA']['error'] = $this->modx->lexicon('user_profile_err_nf');
				}
			}

			$this->modx->error->errors = $this->modx->error->message = null;
			if (empty($_SESSION['HA']['error']) && !$this->modx->user->isAuthenticated() && !empty($login_data)) {

				$_SESSION['HA']['verified'] = 1;
				if (!empty($this->config['loginContext'])) {$login_data['login_context'] = $this->config['loginContext'];}
				if (!empty($this->config['addContexts'])) {$login_data['add_contexts'] = $this->config['addContexts'];}

				// Login
				$response = $this->modx->runProcessor('security/login', $login_data);
				if ($response->isError()) {
					$this->modx->log(modX::LOG_LEVEL_ERROR, '[HybridAuth] error login for user '.$login_data['username'].'. Message: '.implode(', ',$response->getAllErrors()));
					$_SESSION['HA']['error'] = implode(', ',$response->getAllErrors());
				}
			}

			$this->Refresh('login');
		}
	}


	/**
	 * Destroys all sessions
	 *
	 * @return void
	 */
	function Logout() {
		if (is_object($this->Hybrid_Auth)) {
			$this->Hybrid_Auth->logoutAllProviders();
		}
		$response = $this->modx->runProcessor('security/logout');
		if ($response->isError()) {
			$this->modx->log(modX::LOG_LEVEL_ERROR, '[HybridAuth] logout error. Username: '.$this->modx->user->get('username').', uid: '.$this->modx->user->get('id').'. Message: '.implode(', ',$response->getAllErrors()));
			$_SESSION['HA']['error'] = implode(', ',$response->getAllErrors());
		}
		unset($_SESSION['HA::CONFIG']);
		$this->Refresh('logout');
	}


	/**
	 * Gets user profile from service
	 *
	 * @param string $provider Service provider, like Google, Twitter etc.
	 */
	function getServiceProfile($provider = '') {
		$providers = $this->Hybrid_Auth->getConnectedProviders();
		$providerId = ucfirst($provider);
		if (is_array($providers) && in_array($provider, $providers)) {
			/* @var Hybrid_Providers_Google $provider */
			$provider = $this->Hybrid_Auth->getAdapter($providerId);
			$profile = $provider->getUserProfile();
			$array = json_encode($profile);
			return json_decode($array, true);
		}
		else {
			return false;
		}
	}


	/**
	 * Refreshes the current page. If set, can redirects user to logout/login resource.
	 *
	 * @param string $action The action to do
	 * @return void
	 */
	function Refresh($action = '') {
		if ($action == 'login' && $this->config['loginResourceId']) {
			$url = $this->modx->makeUrl($this->config['loginResourceId'],'','','full');
		}
		else if ($action == 'logout' && $this->config['logoutResourceId']) {
			$url = $this->modx->makeUrl($this->config['logoutResourceId'],'','','full');
		}
		else {
			$url = $this->modx->getOption('site_url') . substr($_SERVER['REQUEST_URI'],1);

			if ($pos = strpos($url, '?')) {
				$arr = explode('&',substr($url, $pos+1));
				$url = substr($url, 0, $pos);
				if (count($arr) > 1) {
					foreach ($arr as $k => $v) {
						if (preg_match('/(action|provider|hauth_action)+/i', $v, $matches)) {
							unset($arr[$k]);
						}
					}
					if (!empty($arr)) {
						$url = $url . '?' . implode('&', $arr);
					}

				}
			}
		}

		$this->modx->sendRedirect($url);
	}


	/**
	 * Returns working url
	 *
	 * @return mixed $url
	 */
	function getUrl() {
		$url = $this->modx->getOption('site_url') . substr($_SERVER['REQUEST_URI'], 1);

		if ($pos = strpos($url,'?')) {
			$url .= '&hauth_action=';
		}
		else {
			$url .= '?hauth_action=';
		}
		return $url;
	}


	/**
	 * Sanitizes a string
	 *
	 * @param string $string The string to sanitize
	 * @param integer $length The length of sanitized string
	 * @return string The sanitized string.
	 */
	function Sanitize($string = '', $length = 0) {
		$expr = '/[^-_a-zа-яёЁ0-9@\s\.\,\:\/\\\]+/iu';
		$sanitized = trim(preg_replace($expr, '', $string));

		return !empty($length)
			? substr($sanitized, 0, $length)
			: $sanitized;
	}


	/**
	 * Shorthand for load and run an processor in this component
	 *
	 * {@inheritdoc}
	 */
	function runProcessor($action = '',$scriptProperties = array()) {
		$this->modx->error->errors = $this->modx->error->message = null;

		return $this->modx->runProcessor($action, $scriptProperties, array(
				'processors_path' => $this->config['processorsPath']
			)
		);
	}

}
