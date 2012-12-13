<?php
/**
 * Get an Item
 * 
 * @package hybridauth
 * @subpackage processors
 */
class HybridAuthItemGetProcessor extends modObjectGetProcessor {
	public $classKey = 'HybridAuthItem';
	public $languageTopics = array('hybridauth:default');
	public $objectType = 'hybridauth';
}

return 'HybridAuthItemGetProcessor';