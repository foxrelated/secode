<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Mcard
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Infos.php 2010-10-13 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Mcard_Model_DbTable_Infos extends Engine_Db_Table {

  protected $_name = 'mcard_info';
  protected $_rowClass = 'Mcard_Model_Info';

  public function getVal($level_id, $mp_id) {
    // Get permissions
    $select = $this->select()
                    ->where('level_id = ?', $level_id)
                    ->where('mp_id = ?', $mp_id);

    $result = $this->fetchRow($select);
		if( !empty($result->values) ) {
			return Zend_Json::decode($result->values);
		}
  }

  public function setVal($level_id, $mp_id, $values) {

    // Try to get an existing row
    $select = $this->select()
                    ->where('level_id = ?', $level_id)
                    ->where('mp_id = ?', $mp_id)
                    ->limit(1);

    $row = $this->fetchRow($select);

    // Whoops, create a new row as row doesnot exists
    if (null === $row) {
      $row = $this->createRow();
      $row->level_id = $level_id;
      $row->mp_id = $mp_id;
      $row->values = Zend_Json::encode($values);
    }

    if (null !== $row) {
      if ($values) {
        $row->values = Zend_Json::encode($values);
      }
    }
    $row->save();
    return $this;
  }

}
