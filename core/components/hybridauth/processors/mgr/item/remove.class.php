<?php
/**
 * Remove an Item.
 * 
 * @package hybridauth
 * @subpackage processors
 */
class HybridAuthItemRemoveProcessor extends modObjectRemoveProcessor  {
	public $checkRemovePermission = true;
	public $classKey = 'HybridAuthItem';
	public $languageTopics = array('hybridauth');

}
return 'HybridAuthItemRemoveProcessor';