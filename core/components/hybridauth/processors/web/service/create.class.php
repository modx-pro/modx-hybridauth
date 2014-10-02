<?php

class haUserServiceCreateProcessor extends modObjectCreateProcessor {
	public $classKey = 'haUserService';
	public $languageTopics = array('core:default', 'core:user');
	public $permission = '';
	public $objectType = 'haservice';
	//public $beforeSaveEvent = '';
	//public $afterSaveEvent = '';

	public function beforeSet() {
		$properties = $this->getProperties();

		foreach ($properties as $k => $v) {
			$k = strtolower($k);
			$properties[$k] = $this->modx->stripTags($v);
		}
		$properties['createdon'] = date('Y-m-d H:i:m');

		if (empty($properties['internalKey'])) {
			$this->addFieldError('internalKey', $this->modx->lexicon('field_required'));
		}
		if (empty($properties['provider'])) {
			$this->addFieldError('provider', $this->modx->lexicon('field_required'));
		}

		$this->setProperties($properties);

		return !$this->hasErrors();
	}
}

return 'haUserServiceCreateProcessor';