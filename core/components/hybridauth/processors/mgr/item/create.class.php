<?php
/**
 * Create an Item
 * 
 * @package hybridauth
 * @subpackage processors
 */
class HybridAuthItemCreateProcessor extends modObjectCreateProcessor {
	public $classKey = 'HybridAuthItem';
	public $languageTopics = array('hybridauth');
	public $permission = 'new_document';
	
	public function beforeSet() {
		$alreadyExists = $this->modx->getObject('HybridAuthItem',array(
			'name' => $this->getProperty('name'),
		));
		if ($alreadyExists) {
			$this->modx->error->addField('name',$this->modx->lexicon('hybridauth.item_err_ae'));
		}
		return !$this->hasErrors();
	}
	
}

return 'HybridAuthItemCreateProcessor';