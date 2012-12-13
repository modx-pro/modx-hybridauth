<?php
/**
 * @package hybridauth
 */
require_once MODX_CORE_PATH . 'model/modx/modrequest.class.php';
/**
 * Encapsulates the interaction of MODx manager with an HTTP request.
 *
 * {@inheritdoc}
 *
 * @package hybridauth
 * @extends modRequest
 */
class HybridAuthControllerRequest extends modRequest {
	public $HybridAuth = null;
	public $actionVar = 'action';
	public $defaultAction = 'home';

	function __construct(HybridAuth &$HybridAuth) {
		parent :: __construct($HybridAuth->modx);
		$this->HybridAuth =& $HybridAuth;
	}

	/**
	 * Extends modRequest::handleRequest and loads the proper error handler and
	 * actionVar value.
	 *
	 * {@inheritdoc}
	 */
	public function handleRequest() {
		$this->loadErrorHandler();

		/* save page to manager object. allow custom actionVar choice for extending classes. */
		$this->action = isset($_REQUEST[$this->actionVar]) ? $_REQUEST[$this->actionVar] : $this->defaultAction;

		return $this->_respond();
	}

	/**
	 * Prepares the MODx response to a mgr request that is being handled.
	 *
	 * @access public
	 * @return boolean True if the response is properly prepared.
	 */
	private function _respond() {
		$modx =& $this->modx;
		$HybridAuth =& $this->HybridAuth;

		$viewHeader = include $this->HybridAuth->config['corePath'].'controllers/mgr/header.php';

		$f = $this->HybridAuth->config['corePath'].'controllers/mgr/'.$this->action.'.php';
		if (file_exists($f)) {
			$viewOutput = include $f;
		} else {
			$viewOutput = 'Action not found: '.$f;
		}

		return $viewHeader.$viewOutput;
	}
}