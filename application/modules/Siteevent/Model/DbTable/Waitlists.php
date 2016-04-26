<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Waitlist.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Model_DbTable_Waitlists extends Engine_Db_Table {

    protected $_rowClass = "Siteevent_Model_Waitlist";

    public function getSiteeventWaitlistsPaginator($params = array()) {

        $paginator = Zend_Paginator::factory($this->getSiteeventWaitlistsSelect($params));
        if (!empty($params['page'])) {
            $paginator->setCurrentPageNumber($params['page']);
        }

        if (!empty($params['limit'])) {
            $paginator->setItemCountPerPage($params['limit']);
        }

        return $paginator;
    }

    public function getSiteeventWaitlistsSelect($params = array()) {

        $waitlistTableName = $this->info('name');

        $occurrenceTable = Engine_Api::_()->getDbTable('occurrences', 'siteevent');
        $occurrenceTableName = $occurrenceTable->info('name');

        $userTable = Engine_Api::_()->getItemTable('user');
        $userTableName = $userTable->info('name');

        $select = $this->select()->setIntegrityCheck(false);

        $select->from($waitlistTableName)
            ->joinLeft($occurrenceTableName, "$occurrenceTableName.occurrence_id = $waitlistTableName.occurrence_id", null);

        if (!empty($params['username'])) {
            $userName = $params['username'];
            $select->join($userTableName, "$waitlistTableName.user_id = $userTableName.user_id", 'username');
            $select->where("$userTableName.username  LIKE '%$userName%' OR $userTableName.displayname  LIKE '%$userName%'");
        }

        if (!empty($params['event_id'])) {
            $select->where($occurrenceTableName . '.event_id = ?', $params['event_id']);
        }

        if (!empty($params['occurrence_id'])) {
            $select->where($waitlistTableName . '.occurrence_id = ?', $params['occurrence_id']);
        }

        if (!empty($params['creation_date_start'])) {
            $select->where($waitlistTableName . '.creation_date >= ?', $params['creation_date_start']);
        }

        if (!empty($params['creation_date_end'])) {
            $select->where($waitlistTableName . '.creation_date <= ?', $params['creation_date_end']);
        }

        $select->order($waitlistTableName . '.creation_date ASC');

        return $select;
    }

    public function getColumnValue($params = array()) {

        $waitlistTableName = $this->info('name');

        $columnName = $params['columnName'];
        $select = $this->select()->setIntegrityCheck(false);

        $select->from($waitlistTableName, "$columnName");

        if (!empty($params['event_id'])) {
            $occurrenceTable = Engine_Api::_()->getDbTable('occurrences', 'siteevent');
            $occurrenceTableName = $occurrenceTable->info('name');
            
            $select->joinLeft($occurrenceTableName, "$occurrenceTableName.occurrence_id = $waitlistTableName.occurrence_id", null);
            $select->where($occurrenceTableName . '.event_id = ?', $params['event_id']);
        }

        if (!empty($params['occurrence_id'])) {
            $select->where($waitlistTableName . '.occurrence_id = ?', $params['occurrence_id']);
        }

        if (!empty($params['user_id'])) {
            $select->where($waitlistTableName . '.user_id = ?', $params['user_id']);
        }

        return $select->query()->fetchColumn();
    }

    public function eventsWaitlists($params = array()) {

        $waitlistTableName = $this->info('name');

        $occurrenceTable = Engine_Api::_()->getDbTable('occurrences', 'siteevent');
        $occurrenceTableName = $occurrenceTable->info('name');

        $select = $this->select()->setIntegrityCheck(false);

        $select->from($waitlistTableName)
            ->joinLeft($occurrenceTableName, "$occurrenceTableName.occurrence_id = $waitlistTableName.occurrence_id", null);

        if (!empty($params['event_id'])) {
            $select->where($occurrenceTableName . '.event_id = ?', $params['event_id']);
        }

        if (!empty($params['occurrence_id'])) {
            $select->where($waitlistTableName . '.occurrence_id = ?', $params['occurrence_id']);
        }

        return $select;
    }
    
    public function waitlistCount($params = array()) {
        
        $waitlistTableName = $this->info('name');

        $select = $this->select();

        $select->from($waitlistTableName, array('COUNT(waitlist_id) as total_waitlist'));

        if (!empty($params['occurrence_id'])) {
            $select->where($waitlistTableName . '.occurrence_id = ?', $params['occurrence_id']);
        }

        return $select->query()->fetchColumn();        
    }

}
