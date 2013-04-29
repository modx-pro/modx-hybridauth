<?php

class HybridAuth {

	/* @var Hybrid_Auth $Hybrid_Auth */
	var $Hybrid_Auth;
	/* @var HybridAuthControllerRequest $request*/
	var $request;

	function __construct(modX &$modx,array $config = array()) {
		$this->modx =& $modx;

		set_exception_handler(array($this, 'exceptionHandler'));

		$corePath = $this->modx->getOption('hybridauth.core_path',$config,$this->modx->getOption('core_path').'components/hybridauth/');
		$assetsUrl = $this->modx->getOption('hybridauth.assets_url',$config,$this->modx->getOption('assets_url').'components/hybridauth/');
		$connectorUrl = $assetsUrl.'connector.php';
		$actionUrl = $modx->getOption('site_url') . substr($assetsUrl.'action.php', 1);

		if (empty($config) && !empty($_SESSION['HybridAuth'])) {
			$this->config = $_SESSION['HybridAuth'];
		}
		else {
			$this->config = array_merge(array(
				'assetsUrl' => $assetsUrl
				,'cssUrl' => $assetsUrl.'css/'
				,'jsUrl' => $assetsUrl.'js/'
				,'imagesUrl' => $assetsUrl.'images/'
				,'siteUrl' => $modx->getOption('site_url')

				,'connectorUrl' => $connectorUrl

				,'corePath' => $corePath
				,'modelPath' => $corePath.'model/'
				,'chunksPath' => $corePath.'elements/chunks/'
				,'templatesPath' => $corePath.'elements/templates/'
				,'chunkSuffix' => '.chunk.tpl'
				,'snippetsPath' => $corePath.'elements/snippets/'
				,'processorsPath' => $corePath.'processors/'

				,'rememberme' => true
				,'loginTpl' => 'tpl.HybridAuth.login'
				,'logoutTpl' => 'tpl.HybridAuth.logout'
				,'profileTpl' => 'tpl.HybridAuth.profile'
				,'saltName' => ''
				,'saltPass' => ''
				,'groups' => ''
				,'loginContext' => ''
				,'addContexts' => ''
				,'updateProfile' => true
				,'profileFields' => 'username:25,email:50,fullname:50,phone:12,mobilephone:12,dob:10,gender,address,country,city,state,zip,fax,photo,comment,website'
				,'requiredFields' => 'username,email,fullname'
				,'loginResourceId' => null
				,'logoutResourceId' => null
			),$config);

			$providers = explode(',', $this->config['providers']);
			if (!empty($providers[0])) {
				$this->config['HA'] = array(
					'base_url' => $actionUrl
					,'debug_mode' => !empty($config['debug']) && ($config['debug'] == 'true' || $config['debug'] == 1) ? 1 : 0
					,'debug_file' => MODX_CORE_PATH . 'cache/logs/error.log'
					,'providers' => array()
				);
				foreach ($providers as $provider) {
					$provider = ucfirst(trim($provider));
					$keys = $this->modx->fromJSON($this->modx->getOption('ha.keys.' . $provider));
					if (is_array($keys)) {
						$this->config['HA']['providers'][$provider] = array(
							'enabled' => true
							,'keys' => $keys
						);
					}
					else {
						$this->modx->log(modX::LOG_LEVEL_ERROR, '[HybridAuth] ' . $this->modx->lexicon('ha_err_no_provider_keys', array('provider' => $provider)));
					}
				}
			}
			else {
				$error = '[HybridAuth] ' . $this->modx->lexicon('ha_err_no_providers');
				$this->modx->log(modX::LOG_LEVEL_ERROR, $error);
				$this->modx->error->failure($error);
			}
		}

		$_SESSION['HybridAuth'] = $this->config;

		$this->modx->addPackage('hybridauth',$this->config['modelPath']);
		$this->modx->lexicon->load('hybridauth:default');
		$this->modx->lexicon->load('core:user');

		if (!empty($this->config['HA'])) {
			require_once 'lib/Auth.php';
			$this->Hybrid_Auth = new Hybrid_Auth($this->config['HA']);
		}
	}


	/*
	 * Custom exception handler for Hybrid_Auth
	 *
	 * @param Exception $e Exception object
	 * @return void;
	 * */
	public function exceptionHandler(Exception $e) {
		$code = $e->getCode();
		if ($code < 5) {$level = modX::LOG_LEVEL_ERROR;}
		else {$level = modX::LOG_LEVEL_INFO;}

		$this->modx->log($level, '[HybridAuth] ' . $e->getMessage());
		$this->Refresh();
	}


	/**
	 * Initializes HybridAuth into different contexts.
	 *
	 * @access public
	 * @param string $ctx The context to load. Defaults to web.
	 * @return bool|mixed|object
	 */
	public function initialize($ctx = 'web') {
		switch ($ctx) {
			case 'mgr':
				if (!$this->modx->loadClass('hybridauth.request.HybridAuthControllerRequest',$this->config['modelPath'],true,true)) {
					return 'Could not load controller request handler.';
				}
				/* @var HybridAuthControllerRequest $request */
				$this->request = new HybridAuthControllerRequest($this);
				return $this->request->handleRequest();
			break;
			/*
			case 'connector':
				if (!$this->modx->loadClass('hybridauth.request.HybridAuthConnectorRequest',$this->config['modelPath'],true,true)) {
					return 'Could not load connector request handler.';
				}
				$this->request = new HybridAuthConnectorRequest($this);
				return $this->request->handle();
			break;
			*/
			default: return false;
		}
	}


	/*
	 * Process Hybrid_Auth endpoint
	 *
	 * @return void
	 * */
	function process() {
		require 'lib/Endpoint.php';
		Hybrid_Endpoint::process();
	}


	/*
	 * Checks and login user. Also creates/updated user services profiles
	 *
	 * @param string $provider Remote service to login
	 * @return void
	 * */
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
					return $this->Login($provider);
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
		return $this->loadTpl();
	}


	/*
	 * Destroys all sessions
	 *
	 * @return void
	 * */
	function Logout() {
		$this->Hybrid_Auth->logoutAllProviders();
		$response = $this->modx->runProcessor('security/logout');
		if ($response->isError()) {
			$this->modx->log(modX::LOG_LEVEL_ERROR, '[HybridAuth] logout error. Username: '.$this->modx->user->get('username').', uid: '.$this->modx->user->get('id').'. Message: '.implode(', ',$response->getAllErrors()));
			$_SESSION['HA']['error'] = implode(', ',$response->getAllErrors());
		}
		unset($_SESSION['HA::CONFIG']);
		$this->Refresh('logout');
	}


	/*
	 * Gets user profile from service
	 *
	 * @param string $provider Service provider, like Google, Twitter etc.
	 * */
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


	/*
	 * Return form for update user profile
	 *
	 * @param array $data Array with user fields
	 * @return mixed $chunk
	 * */
	function getProfile($data = array()) {
		if (!$this->modx->user->isAuthenticated()) {
			$id = $this->modx->getOption('unauthorized_page');
			if ($id != $this->modx->resource->id) {
				$this->modx->sendForward($id);
			}
			else {
				header('HTTP/1.0 401 Unauthorized');
				return 'HybridAuth error: 401 Unauthorized';
			}
		}
		/* @var modUser $user */
		/* @var modUserProfile $profile */
		if (empty($data) && $user = $this->modx->getObject('modUser', $this->modx->user->id)) {
			$profile = $user->getOne('Profile');
			$arr = array_merge($user->toArray(), $profile->toArray());
		}
		else {
			$arr = $data;
		}

		$profiles = $this->modx->user->getMany('Services');
		/* @var haUserService $v */
		$add = array();
		foreach ($profiles as $v) {
			$add = array_merge($add, $v->toArray(strtolower($v->get('provider').'.')));
		}

		$url = $this->getUrl();
		$arr = array_merge($arr, $add, array(
			'login_url' => $url.'login'
			,'logout_url' => $url.'logout'
		));
		return $this->modx->getChunk($this->config['profileTpl'], $arr);
	}


	/*
	 * Updates user profile
	 *
	 * $param array $fields Array with new values
	 * */
	function updateProfile($fields = array()) {
		if (!$this->modx->user->isAuthenticated()) {
			$this->Refresh();
		}

		$data = array();
		$profileFields = explode(',', $this->config['profileFields']);
		foreach ($profileFields as $field) {
			@list($key, $length) = explode(':', $field);

			if (!empty($fields[$key])) {
				$data[$key] = $this->Sanitize($fields[$key], $length);
			}
		}

		$data['requiredFields'] = explode(',', $this->config['requiredFields']);
		if ($this->modx->user->class_key != 'haUser') {
			$this->modx->user->class_key = 'haUser';
			$this->modx->user->save();
		}

		$response = $this->runProcessor('web/user/update', $data);
		if ($response->isError()) {
			foreach ($response->errors as $error) {
				$data['error.'.$error->field] = $error->message;
			}
			$data['success'] = 0;
		}
		else {$data['success'] = 1;}

		return $this->getProfile($data);
	}


	/*
	 * Refreshes the current page. If set, can redirects user to logout/login resource.
	 *
	 * @param string $action The action to do
	 * @return void
	 * */
	function Refresh($action = '') {
		if ($action == 'login' && $this->config['loginResourceId']) {
			$url = $this->modx->makeUrl($this->config['loginResourceId'],'','','full');
		}
		else if ($action == 'logout' && $this->config['logoutResourceId']) {
			$url = $this->modx->makeUrl($this->config['logoutResourceId'],'','','full');
		}
		else {
			$url = $this->config['siteUrl'] . substr($_SERVER['REQUEST_URI'],1);

			if ($pos = strpos($url, '?')) {
				$arr = explode('&',substr($url, $pos+1));
				$url = substr($url, 0, $pos);
				if (count($arr) > 1) {
					foreach ($arr as $k => $v) {
						if (preg_match('/(action|provider)+/i', $v, $matches)) {
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


	/*
	 * Loads separate chunks for guest and user
	 *
	 * @return mixed $chunk
	 * */
	function loadTpl() {
		$url = $this->getUrl();
		$error = '';

		if (!empty($_SESSION['HA']['error'])) {
			$error = $_SESSION['HA']['error'];
			unset($_SESSION['HA']['error']);
		}

		if ($this->modx->user->isAuthenticated()) {
			$profiles = $this->modx->user->getMany('Services');

			/* @var haUserService $v */
			$add = array();
			foreach ($profiles as $v) {
				$add = array_merge($add, $v->toArray(strtolower($v->get('provider').'.')));
			}
			$user = $this->modx->user->toArray();
			$profile = $this->modx->user->getOne('Profile')->toArray();

			$arr = array_merge($user,$profile,$add, array(
				'login_url' => $url.'login'
				,'logout_url' => $url.'logout'
				,'error' => $error
			));
			return $this->modx->getChunk($this->config['logoutTpl'], $arr);
		}
		else {
			$arr = array(
				'login_url' => $url.'login'
				,'logout_url' => $url.'logout'
				,'error' => $error
			);
			return $this->modx->getChunk($this->config['loginTpl'], $arr);
		}
	}


	/*
	 * Returns working url
	 *
	 * @return mixed $url
	 * */
	function getUrl() {
		$url = $this->config['siteUrl'] . substr($_SERVER['REQUEST_URI'], 1);

		if ($pos = strpos($url,'?')) {
			$url .= '&action=';
		}
		else {
			$url .= '?action=';
		}
		return $url;
	}


	/*
	 * Sanitizes a string
	 *
	 * @param string $string The string to sanitize
	 * @param integer $length The length of sanitized string
	 * @return string The sanitized string.
	 * */
	function Sanitize($string = '', $length = 0) {
		$expr = '/[^-_a-zа-яё0-9@\s\.\,\:\/\\\]+/iu';
		$sanitized = trim(preg_replace($expr, '', $string));

		return substr($sanitized, 0, $length);
	}


	/*
	 * Shorthand for load and run an processor in this component
	 *
	 * {@inheritdoc}
	 * */
	function runProcessor($action = '',$scriptProperties = array()) {
		$this->modx->error->errors = $this->modx->error->message = null;

		return $this->modx->runProcessor($action, $scriptProperties, array(
				'processors_path' => $this->config['processorsPath']
			)
		);
	}

}
