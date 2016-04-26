<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Recentlyviewitems.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
 
class Sesvideo_Model_DbTable_Recentlyviewitems extends Engine_Db_Table {
  protected $_name = 'sesvideo_recentlyviewitems';
  protected $_rowClass = 'Sesvideo_Model_Recentlyviewitem';
  public function getitem($params = array()) {
    if ($params['type'] == 'sesvideo_chanel') {
      $itemTable = Engine_Api::_()->getItemTable('sesvideo_chanel');
      $itemTableName = $itemTable->info('name');
      $fieldName = 'chanel_id';
    } else {
      $itemTable = Engine_Api::_()->getItemTable('sesvideo_video');
      $itemTableName = $itemTable->info('name');
      $fieldName = 'video_id';
      $not = true;
    }
		$subquery = $this->select()->from($this->info('name'),array('*','MAX(creation_date)'))->group($this->info('name').".resource_id")->order($this->info('name').".resource_id DESC")->where($this->info('name').'.resource_type =?', $params['type']);		
    $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(array('engine4_sesvideo_recentlyviewitems' => $subquery))
            ->where($this->info('name') .'.resource_type = ?', $params['type'])
            ->order('engine4_sesvideo_recentlyviewitems.creation_date DESC')
            ->group($this->info('name') . '.resource_id')
            ->limit($params['limit']);
    if ($params['criteria'] == 'by_me') {
      $select->where($this->info('name') . '.owner_id =?', Engine_Api::_()->user()->getViewer()->getIdentity());
    } else if ($params['criteria'] == 'by_myfriend') {
      /* friends array */
      $friendIds = Engine_Api::_()->user()->getViewer()->membership()->getMembershipsOfIds();
      if (count($friendIds) == 0)
        return array();
      $select->where($this->info('name') . ".owner_id IN ('" . implode(',', $friendIds) . "')");
    }
    $select->joinLeft($itemTableName, $itemTableName . ".$fieldName =  " . $this->info('name') . '.resource_id', array('*'));
		if (isset($not)) {
				if (Engine_Api::_()->getApi('settings', 'core')->getSetting('video.enable.watchlater', 1)) {
				$viewer = Engine_Api::_()->user()->getViewer();
				$user_id = $viewer->getIdentity();
				$watchLaterTable = Engine_Api::_()->getDbTable('watchlaters', 'sesvideo')->info('name');
				$select = $select->setIntegrityCheck(false);
				$select = $select->joinLeft($watchLaterTable, '(' . $watchLaterTable . '.video_id = ' . $this->info('name') . '.resource_id AND ' . $watchLaterTable . '.owner_id = ' . $user_id . ')', array('watchlater_id'));
			}			
    }
    return Zend_Paginator::factory($select);
  }
}