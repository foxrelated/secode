<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Locations.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Model_DbTable_Locations extends Engine_Db_Table {

  protected $_rowClass = "Sitegroup_Model_Location";

  /**
   * Get location
   *
   * @param array $params
   * @return object
   */
  public function getLocation($params=array()) {

    $locationFieldEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.locationfield', 1);
    if ($locationFieldEnable) {
   
      $locationName = $this->info('name');

      $select = $this->select();
      if (isset($params['id']) && isset($params['mapshow']) && $params['mapshow'] == 'Map Tab') {
        $select->where('group_id = ?', $params['id']);
        if (!empty($params['mainlocationId'])) {
					$select->where('location_id <> ?', $params['mainlocationId']);
        }
        
        $select->order('location_id DESC');
        return Zend_Paginator::factory($select);
      }
      elseif (isset($params['id'])) {
        $select->where('group_id = ?', $params['id']);
        if (isset($params['location_id']) && !empty($params['location_id'])) {
					$select->where('location_id = ?', $params['location_id']);
        }
        return $this->fetchRow($select);
      }

      if (isset($params['group_ids'])) {

        $idsStr = (string) ( is_array($params['group_ids']) ? "'" . join("', '", $params['group_ids']) . "'" : $params['group_ids'] );

        $select->where('group_id IN (?)', new Zend_Db_Expr($idsStr));
        return $this->fetchAll($select);
      }
    }
  }
  public function getLocationId ($group_id, $location) {

		$locationName = $this->info('name');
		$select = $this->select()->from($locationName, 'location_id');
		$location_id = $select->where('group_id = ?', $group_id)->where('location = ?', $location)->query()
												->fetchColumn();
		return $location_id;

  }

}
?>