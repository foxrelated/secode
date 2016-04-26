<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: Locations.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_Model_DbTable_Locations extends Engine_Db_Table {

  protected $_rowClass = "List_Model_Location";

  /**
   * Get location
   *
   * @param array $params
   * @return object
   */
  public function getLocation($params=array()) {

    $locationFieldEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('list.locationfield', 1);
    if ($locationFieldEnable) {
   
      $locationName = $this->info('name');

      $select = $this->select();
      if (isset($params['id'])) {
        $select->where('listing_id = ?', $params['id']);
        return $this->fetchRow($select);
      }

      if (isset($params['listing_ids'])) {

        $idsStr = (string) ( is_array($params['listing_ids']) ? "'" . join("', '", $params['listing_ids']) . "'" : $params['listing_ids'] );

        $select->where('listing_id IN (?)', new Zend_Db_Expr($idsStr));
        return $this->fetchAll($select);
      }
    }
  }

}