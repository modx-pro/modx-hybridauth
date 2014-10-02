<?php

class haUserServiceGetListProcessor extends modObjectGetListProcessor {
	public $classKey = 'haUserService';
	public $objectType = 'ha.service';
	public $defaultSortField = 'createdon';
	public $defaultSortDirection = 'DESC';
	public $languageTopics = array('hybridauth:default');


	public function prepareQueryBeforeCount(xPDOQuery $c) {
		$userId = (int)$this->getProperty('user_id');
		if ($userId > 0) {
			$c->where(array(
					'internalKey' => $userId,
				)
			);
		}

		return $c;
	}


	public function prepareQueryAfterCount(xPDOQuery $c) {
		$c->select($this->modx->getSelectColumns($this->classKey, $this->classKey));
		$c->select(array(
				'IF(emailverified IS NULL OR LENGTH(emailverified) = 0, email, emailverified) AS email',
			)
		);

		return $c;
	}


	public function prepareRow(xPDOObject $object) {
		$row = $object->toArray();
		$row['hash'] = md5(strtolower($row['email']));

		return $row;
	}
}

return 'haUserServiceGetListProcessor';
