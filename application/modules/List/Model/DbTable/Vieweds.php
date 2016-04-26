<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: Viewads.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_Model_DbTable_Vieweds extends Engine_Db_Table {

  protected $_rowClass = "List_Model_Viewed";

  public function getVieweds($listing_id, $viewer_id) {

    $select = $this->select()
							->where('listing_id = ?', $listing_id)
							->where('viewer_id = ?', $viewer_id);

    return $this->fetchRow($select);
  }

  public function setVieweds($listing_id, $viewer_id) {

    $row = $this->getVieweds($listing_id, $viewer_id);

    if (empty($row)) {
      $row = $this->createRow();
      $row->listing_id = $listing_id;
      $row->viewer_id = $viewer_id;
    }

    $row->date = new Zend_Db_Expr('NOW()');
    $row->save();
  }

}