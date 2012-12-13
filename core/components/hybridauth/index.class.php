<?php
/**
 * The main manager controller for HybridAuth.
 *
 * @package hybridauth
 */

require_once dirname(__FILE__) . '/model/hybridauth/hybridauth.class.php';

abstract class HybridAuthMainController extends modExtraManagerController {
	/** @var HybridAuth $hybridauth */
	public $hybridauth;

	public function initialize() {
		$this->HybridAuth = new HybridAuth($this->modx);
		
		$this->modx->regClientCSS($this->HybridAuth->config['cssUrl'].'mgr.css');
		$this->modx->regClientStartupScript($this->HybridAuth->config['jsUrl'].'mgr/hybridauth.js');
		$this->modx->regClientStartupHTMLBlock('<script type="text/javascript">
		Ext.onReady(function() {
			HybridAuth.config = '.$this->modx->toJSON($this->HybridAuth->config).';
			HybridAuth.config.connector_url = "'.$this->HybridAuth->config['connectorUrl'].'";
		});
		</script>');
		
		return parent::initialize();
	}

	public function getLanguageTopics() {
		return array('hybridauth:default');
	}

	public function checkPermissions() { return true;}
}

class IndexManagerController extends HybridAuthMainController {
	public static function getDefaultController() { return 'home'; }
}