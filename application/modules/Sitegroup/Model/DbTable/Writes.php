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
class Sitegroup_Model_DbTable_Writes extends Engine_Db_Table {

  protected $_rowClass = "Sitegroup_Model_Write";

  public function writeContent($group_id) {
    $select = $this->select()->where('group_id = ?', $group_id);
    return $this->fetchRow($select);
  }

  public function setWriteContent($group_id, $text) {

    $this->delete(array('group_id = ?' => $group_id));
    $row = $this->createRow();
    $row->text = $text;
    $row->group_id = $group_id;
    $row->save();
  }
}
?>