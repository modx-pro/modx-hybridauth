<?php

class haUserServiceGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'haUserService';
    public $defaultSortField = 'createdon';
    public $defaultSortDirection = 'DESC';
    public $languageTopics = array('hybridauth');


    /**
     * @param xPDOQuery $c
     *
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $userId = (int)$this->getProperty('user_id');
        if ($userId > 0) {
            $c->where(array(
                    'internalKey' => $userId,
                )
            );
        }

        return $c;
    }


    /**
     * @param xPDOQuery $c
     *
     * @return xPDOQuery
     */
    public function prepareQueryAfterCount(xPDOQuery $c)
    {
        $c->select($this->modx->getSelectColumns($this->classKey, $this->classKey));
        $c->select(array(
                'IF(emailverified IS NULL OR LENGTH(emailverified) = 0, email, emailverified) AS email',
            )
        );

        return $c;
    }


    /**
     * @param xPDOObject $object
     *
     * @return array
     */
    public function prepareRow(xPDOObject $object)
    {
        $array = $object->toArray();
        $array['hash'] = md5(strtolower($array['email']));

        $array['actions'] = array();
        // Remove
        $array['actions'][] = array(
            'cls' => '',
            'icon' => 'icon icon-trash-o action-red',
            'title' => $this->modx->lexicon('ha.service_remove'),
            'multiple' => $this->modx->lexicon('ha.services_remove'),
            'action' => 'removeItem',
            'button' => true,
            'menu' => true,
        );

        return $array;
    }
}

return 'haUserServiceGetListProcessor';
