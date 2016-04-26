<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Vieweds.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Model_DbTable_Vieweds extends Engine_Db_Table {

  protected $_rowClass = "Siteevent_Model_Viewed";

  public function setVieweds($event_id, $viewer_id) {

    if (empty($viewer_id)) {
      return;
    }

    //GET IF ENTRY IS EXIST FOR SAME VIEWER ID
    $select = $this->select()
            ->where('event_id = ?', $event_id)
            ->where('viewer_id = ?', $viewer_id);
    $vieweds = $this->fetchRow($select);

    if (empty($vieweds)) {
      $row = $this->createRow();
      $row->event_id = $event_id;
      $row->viewer_id = $viewer_id;
      $row->date = new Zend_Db_Expr('NOW()');
      $row->save();
    } else {
      $vieweds->date = new Zend_Db_Expr('NOW()');
      $vieweds->save();
    }

    $this->deleteOld($viewer_id);
  }

  public function deleteOld($viewer_id) {
    if (empty($viewer_id))
      return;
    //DELETE ENTRIES IF MORE THAN 10
    $count = $this->select()
            ->from($this->info('name'), array('COUNT(viewed_id) as total_entries'))
            ->where('viewer_id = ?', $viewer_id)
            ->query()
            ->fetchColumn();
    
    $noOfOld = $count - 10;
    if ($noOfOld > 0) {
      //DELETE ENTRIES IF MORE THAN 10
      $oldIds = $select = $this->select()
                      ->from($this->info('name'), array('viewed_id'))
                      ->where('viewer_id = ?', $viewer_id)
                      ->order('date ASC')
                      ->limit($noOfOld)
                      ->query()->fetchAll(Zend_Db::FETCH_COLUMN);

      if ($oldIds)
        $this->delete(array('viewed_id IN(?)' => $oldIds));
    }
  }

}
