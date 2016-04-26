<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Rejecteds.php 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Suggestion_Model_DbTable_Rejecteds extends Engine_Db_Table {

  protected $_name = 'suggestion_rejected';
  protected $_rowClass = 'Suggestion_Model_Rejected';

  public function setSettings($entity, $entity_id) {
    $viewer = Engine_Api::_()->user()->getViewer();
    if (empty($viewer) || empty($entity) || empty($entity_id)) {
      return;
    }

    $row = $this->createRow();
    $row->owner_id = $viewer->getIdentity();
    $row->entity = $entity;
    $row->entity_id = $entity_id;
    $row->save();
  }

  public function getRejectIds($entity, $entity_id = null) {
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !strstr($entity, "sitereview") ) {
      $getSuggMod = Engine_Api::_()->suggestion()->getModSettings($entity, 'quality');
      if (empty($entity) || empty($viewer) || empty($getSuggMod)) {
        return;
      }
    }

    $select = $this->select()
                    ->where('entity = ?', $entity);
    if (empty($entity_id)) { // Return "Rows" of any content which rejected by loggden user.
      $select->where('owner_id = ?', $viewer->getIdentity());
      $colName = 'entity_id';
    } else {// Return "Rows" of any spacific content.
      $select->where('entity_id = ?', $entity_id);
      $colName = 'owner_id';
    }

    $select = $select->query()->fetchAll();
    $rejectIdsStr = 0;
    foreach ($select as $notInUser) {
      $rejectIdsStr .= "," . $notInUser[$colName];
    }
    $rejectIdsStr = trim($rejectIdsStr, ",");

    return $rejectIdsStr;
  }
}