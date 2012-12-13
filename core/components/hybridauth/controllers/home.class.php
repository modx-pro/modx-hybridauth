<?php
/**
 * The home manager controller for HybridAuth.
 *
 * @package hybridauth
 */
class HybridAuthHomeManagerController extends HybridAuthMainController {
	public function process(array $scriptProperties = array()) {}
	
	public function getPageTitle() { return $this->modx->lexicon('hybridauth'); }
	
	public function loadCustomCssJs() {
		$this->modx->regClientStartupScript($this->HybridAuth->config['jsUrl'].'mgr/widgets/items.grid.js');
		$this->modx->regClientStartupScript($this->HybridAuth->config['jsUrl'].'mgr/widgets/home.panel.js');
		$this->modx->regClientStartupScript($this->HybridAuth->config['jsUrl'].'mgr/sections/home.js');
	}
	
	public function getTemplateFile() {
		return $this->HybridAuth->config['templatesPath'].'home.tpl';
	}
}