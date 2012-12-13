<?php
/**
 * Update an Item
 * 
 * @package hybridauth
 * @subpackage processors
 */
class HybridAuthItemUpdateProcessor extends modObjectUpdateProcessor {
	public $classKey = 'HybridAuthItem';
	public $languageTopics = array('hybridauth');
	public $permission = 'update_document';
}

return 'HybridAuthItemUpdateProcessor';