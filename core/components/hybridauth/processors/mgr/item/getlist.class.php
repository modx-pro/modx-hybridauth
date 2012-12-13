<?php
/**
 * Get a list of Items
 *
 * @package hybridauth
 * @subpackage processors
 */
class HybridAuthItemGetListProcessor extends modObjectGetListProcessor {
	public $classKey = 'HybridAuthItem';
	public $defaultSortField = 'id';
	public $defaultSortDirection  = 'DESC';
	public $renderers = '';
	
	public function prepareQueryBeforeCount(xPDOQuery $c) {
		return $c;
	}

	public function prepareRow(xPDOObject $object) {
		$array = $object->toArray();
		return $array;
	}
	
}

return 'HybridAuthItemGetListProcessor';