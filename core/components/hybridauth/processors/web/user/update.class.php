<?php

if (!class_exists('haUserUpdateBaseProcessor', false)) {
    if (class_exists('modUserUpdateProcessor')) {
        class haUserUpdateBaseProcessor extends modUserUpdateProcessor
        {
        }
    } elseif (class_exists(\MODX\Revolution\Processors\Security\User\Update::class)) {
        class haUserUpdateBaseProcessor extends \MODX\Revolution\Processors\Security\User\Update
        {
        }
    } else {
        class haUserUpdateBaseProcessor extends modObjectUpdateProcessor
        {
        }
    }
}

class haUserUpdateProcessor extends haUserUpdateBaseProcessor
{
    public $classKey = 'modUser';
    public $languageTopics = array('core:default', 'core:user');
    public $permission = '';
    public $objectType = 'user';
    public $beforeSaveEvent = 'OnBeforeUserFormSave';
    public $afterSaveEvent = 'OnUserFormSave';


    /**
     * @return bool|string
     */
    public function initialize()
    {
        $this->setProperty('id', $this->modx->user->id);

        return parent::initialize();
    }


    /**
     * @return bool
     */
    public function beforeSet()
    {
        $fields = $this->getProperty('requiredFields', '');
        if (!empty($fields) && is_array($fields)) {
            foreach ($fields as $field) {
                $tmp = trim($this->getProperty($field, null));
                if ($field == 'email' && !preg_match('/^[^@а-яА-Я]+@[^@а-яА-Я]+(?<!\.)\.[^\.а-яА-Я]{2,}$/m', $tmp)) {
                    $this->addFieldError('email', $this->modx->lexicon('user_err_not_specified_email'));
                } else {
                    if (empty($tmp)) {
                        $this->addFieldError($field, $this->modx->lexicon('field_required'));
                    }
                }
            }
        }

        return parent::beforeSet();
    }

}

return 'haUserUpdateProcessor';
