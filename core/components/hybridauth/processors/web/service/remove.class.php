<?php

class haUserServiceRemoveProcessor extends modObjectRemoveProcessor
{
    public $classKey = 'haUserService';
    public $languageTopics = array('core:default', 'core:user');
    public $permission = '';
    public $checkRemovePermission = false;


    /**
     * @return bool|null|string
     */
    public function initialize()
    {
        $provider = $this->getProperty('provider');
        $this->object = $this->modx->getObject($this->classKey, array(
            'internalKey' => $this->modx->user->id,
            'provider' => $provider,
        ));

        if (empty($this->object)) {
            return $this->modx->lexicon($this->objectType . '_err_nfs');
        }

        return true;
    }
}

return 'haUserServiceRemoveProcessor';
