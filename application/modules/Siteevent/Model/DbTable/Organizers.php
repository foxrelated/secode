<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Videos.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Model_DbTable_Organizers extends Engine_Db_Table {

    protected $_rowClass = "Siteevent_Model_Organizer";

    public function getPaginator($params = array()) {

        $paginator = Zend_Paginator::factory($this->getSelect($params));
        if (!empty($params['page'])) {
            $paginator->setCurrentPageNumber($params['page']);
        }

        if (!empty($params['limit'])) {
            $paginator->setItemCountPerPage($params['limit']);
        }

        return $paginator;
    }

    //GET SITEEVENT SELECT QUERY
    public function getSelect($params = array()) {
        $tableName = $this->info('name');
        $select = $this->select()
                ->setIntegrityCheck(false)
                ->from($tableName);
        if (isset($params['creator_id']) && $params['creator_id']) {
            $select->where($tableName . '.creator_id = ?', $params['creator_id']);
        }

        if (isset($params['orderby']) && $params['orderby']) {
            $select->order($tableName . '.' . $params['orderby']);
        }

        $select->order($tableName . '.organizer_id DESC');
        return $select;
    }

    public function getOrganizer($params = array()) {
        $tableName = $this->info('name');
        $select = $this->select()
                ->setIntegrityCheck(false)
                ->from($tableName);
        if (isset($params['creator_id']) && $params['creator_id']) {
            $select->where($tableName . '.creator_id = ?', $params['creator_id']);
        }

        if (isset($params['equal_title']) && $params['equal_title']) {
            $select->where($tableName . '.title = ?', $params['equal_title']);
        }

        if (isset($params['orderby']) && $params['orderby']) {
            $select->order($tableName . '.' . $params['orderby']);
        }

        $select->order($tableName . '.organizer_id DESC');
        return $this->fetchRow($select);
    }

}