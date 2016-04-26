<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Writes.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Model_DbTable_Announcements extends Engine_Db_Table {

  protected $_name = 'sitegroup_announcements';
  protected $_rowClass = 'Sitegroup_Model_Announcement';

  public function announcements($params = array(), $fetchColumns = array()) {
  
    $announcementTableName = $this->info('name');
    
    $select = $this->select();
    
    if (!empty($fetchColumns)) {
        $select->from($announcementTableName, $fetchColumns);
    }    
    
    if(isset($params['group_id']) && !empty($params['group_id'])) {
        $select->where('group_id = ?', $params['group_id']);
    }   
    
    if (isset($params['hideExpired']) && !empty($params['hideExpired'])) {
			$select->where($announcementTableName . '.status = ?', 1)
			      ->where($announcementTableName . '. startdate <= ?', date('y-m-d'))
            ->where($announcementTableName . '. expirydate >= ?', date('y-m-d'));    
    }
    
    if (isset($params['limit']) && !empty($params['limit'])) {
        $select->limit($params['limit']);
    }
    
    $select->order($announcementTableName . '.creation_date DESC');
    
    return $this->fetchAll($select);
  }
}