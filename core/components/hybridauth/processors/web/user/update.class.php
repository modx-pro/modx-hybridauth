<?php
require MODX_CORE_PATH.'model/modx/processors/security/user/update.class.php';

class haUserUpdateProcessor extends modUserUpdateProcessor {
	public $classKey = 'haUser';
	public $languageTopics = array('core:default','core:user');
	public $permission = '';
	public $objectType = 'hauser';
	public $beforeSaveEvent = 'OnBeforeUserFormSave';
	public $afterSaveEvent = 'OnUserFormSave';


	/**
	 * {@inheritDoc}
	 * @return boolean|string
	 */
	public function initialize() {
		$this->setProperty('id', $this->modx->user->id);
		return parent::initialize();
	}


	/**
	 * {@inheritDoc}
	 * @return boolean
	 */
	public function beforeSet() {
		$fields = $this->getProperty('requiredFields', '');
		if (!empty($fields) && is_array($fields)) {
			foreach ($fields as $field) {
				$tmp = trim($this->getProperty($field,null));
				if ($field == 'email' && !filter_var($tmp, FILTER_VALIDATE_EMAIL)) {
						$this->addFieldError('email', $this->modx->lexicon('user_err_not_specified_email'));
				}
				else if (empty($tmp)) {
					$this->addFieldError($field, $this->modx->lexicon('field_required'));
				}
			}
		}

		return parent::beforeSet();
	}

}

return 'haUserUpdateProcessor';