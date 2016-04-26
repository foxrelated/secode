<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Announcements.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Model_DbTable_Announcements extends Engine_Db_Table {

    protected $_name = 'siteevent_announcements';
    protected $_rowClass = 'Siteevent_Model_Announcement';

    public function announcements($event_id, $showExpired = 0, $limit, $fetchColumns = array()) {

        $announcementTableName = $this->info('name');

        $select = $this->select();
        
        if (!empty($fetchColumns)) {
            $select->from($announcementTableName, $fetchColumns);
        }
        $select->where('event_id = ?', $event_id);
        
        if (empty($showExpired)) {
            $select->where($announcementTableName . '.status = ?', 1)
                    ->where($announcementTableName . '. startdate <= ?', date('y-m-d'))
                    ->where($announcementTableName . '. expirydate >= ?', date('y-m-d'));
        }
        $select->order($announcementTableName . '.creation_date DESC');

        if (!empty($limit)) {
            $select->limit($limit);
        }

        return $this->fetchAll($select);
    }

}