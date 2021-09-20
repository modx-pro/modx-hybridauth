<?php

class haUserServiceRemoveProcessor extends modObjectProcessor
{
    public $classKey = 'haUserService';
    public $languageTopics = array('hybridauth');
    public $permission = 'save_user';


    /**
     * @return array|string
     */
    public function process()
    {
        if (!$this->checkPermissions()) {
            return $this->failure($this->modx->lexicon('access_denied'));
        }
        $ids = $this->modx->fromJSON($this->getProperty('ids'));
        if (empty($ids)) {
            return $this->failure($this->modx->lexicon('ha_service_err_ns'));
        }
        foreach ($ids as $id) {
            /** @var haUserService $object */
            if (!$object = $this->modx->getObject($this->classKey, (int)$id)) {
                return $this->failure($this->modx->lexicon('ha_service_err_nf'));
            }
            $object->remove();
        }

        return $this->success();
    }
}

return 'haUserServiceRemoveProcessor';
