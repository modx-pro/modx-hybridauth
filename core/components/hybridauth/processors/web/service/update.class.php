<?php

class haUserServiceUpdateProcessor extends modObjectUpdateProcessor
{
    public $classKey = 'haUserService';
    public $languageTopics = ['core:default', 'core:user'];
    public $permission = '';


    /**
     * @return bool
     */
    public function beforeSet()
    {
        $properties = $this->getProperties();

        foreach ($properties as $k => $v) {
            $k = strtolower($k);
            if (is_array($v)) {
                foreach ($v as &$v2) {
                    $v2 = $this->modx->stripTags($v2);
                }
                $properties[$k] = $v;
            } else {
                $properties[$k] = $this->modx->stripTags($v);
            }
        }

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

return 'haUserServiceUpdateProcessor';
