<?php

class HybridAuth {

	/* @var Hybrid_Auth $Hybrid_Auth */
	var $Hybrid_Auth;
	/* @var HybridAuthControllerRequest $request*/
	var $request;

	function __construct(modX &$modx,array $config = array()) {
		$this->modx =& $modx;

		$corePath = $this->modx->getOption('hybridauth.core_path',$config,$this->modx->getOption('core_path').'components/hybridauth/');
		$assetsUrl = $this->modx->getOption('hybridauth.assets_url',$config,$this->modx->getOption('assets_url').'components/hybridauth/');
		$connectorUrl = $assetsUrl.'connector.php';
		$actionUrl = $modx->getOption('site_url') . substr($assetsUrl.'action.php', 1);

		$this->config = array_merge(array(
			'assetsUrl' => $assetsUrl
			,'cssUrl' => $assetsUrl.'css/'
			,'jsUrl' => $assetsUrl.'js/'
			,'imagesUrl' => $assetsUrl.'images/'

			,'connectorUrl' => $connectorUrl

			,'corePath' => $corePath
			,'modelPath' => $corePath.'model/'
			,'chunksPath' => $corePath.'elements/chunks/'
			,'templatesPath' => $corePath.'elements/templates/'
			,'chunkSuffix' => '.chunk.tpl'
			,'snippetsPath' => $corePath.'elements/snippets/'
			,'processorsPath' => $corePath.'processors/'

			,'providers' => ''
		),$config);

		$this->modx->addPackage('hybridauth',$this->config['modelPath']);
		$this->modx->lexicon->load('hybridauth:default');

		if (!empty($_SESSION['HA::CONFIG']['config'])) {
			$ha_config = unserialize($_SESSION['HA::CONFIG']['config']);
		}
		else {
			$providers = explode(',', $this->config['providers']);
			if (!empty($providers[0])) {
				$ha_config = array(
					'base_url' => $actionUrl
					,'providers' => array()
				);
				foreach ($providers as $provider) {
					$provider = ucfirst(trim($provider));
					$keys = $this->modx->fromJSON($this->modx->getOption('ha.keys.' . $provider));
					if (is_array($keys)) {
						$ha_config['providers'][$provider] = array(
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

		if (!empty($ha_config)) {
			require_once 'lib/Auth.php';
			$this->Hybrid_Auth = new Hybrid_Auth($ha_config);
		}


	}

	/**
	 * Initializes HybridAuth into different contexts.
	 *
	 * @access public
	 * @param string $ctx The context to load. Defaults to web.
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

	function process() {
		require 'lib/Endpoint.php';
		Hybrid_Endpoint::process();
	}

	function Login($provider = '') {
		$provider = $this->Hybrid_Auth->authenticate($provider);
		return $this->loadTpl();
	}

	function Logout() {
		$this->Hybrid_Auth->logoutAllProviders();
	}

	function loadTpl() {
		$data = unserialize($this->Hybrid_Auth->getSessionData());
		return '<pre>'.print_r($data,1).'</pre>';
	}

	function getUserProfile($provider = '') {
		$providers = $this->Hybrid_Auth->getConnectedProviders();
		$providerId = ucfirst($provider);
		if (is_array($providers) && in_array($provider, $providers)) {
			/* @var Hybrid_Provider_Model $provider */
			$provider = $this->Hybrid_Auth->getAdapter($providerId);
			return $provider->getUserProfile();
		}
		else {
			return false;
		}
	}

}
