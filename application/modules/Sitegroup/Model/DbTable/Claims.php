<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Claims.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Model_DbTable_Claims extends Engine_Db_Table {

  protected $_rowClass = 'Sitegroup_Model_Claim';

	/**
   * Return status
   *
   * @param array params
   * @return status
   */
  public function getClaimStatus($params) {

  	$select = $this->select()->from($this->info('name'), 'status');
		if(isset($params['group_id']) && !empty($params['group_id'])) {
			$select = $select->where('group_id = ?', $params['group_id']);
		}
		if(isset($params['viewer_id']) && !empty($params['viewer_id'])) {
			$select = $select->where('user_id = ?', $params['viewer_id']);
		}
    return $this->fetchRow($select);
  }

	/**
   * Return viewer cliams
   *
   * @param int $viewer_id
   */
	public function getViewerClaims($viewer_id) {

    $claim_id = 0;
    $claim_id = $this
              ->select()
              ->from($this->info('name'), array('claim_id'))
              ->where("user_id = ?", $viewer_id)
              ->order('creation_date')
              ->query()
              ->fetchColumn();
    return $claim_id;	
	}

  /**
   * Gets claim groups
   *
   * @param string $viewer_id
   * @param  Zend_Db_Table_Select
   */
  public function getMyClaimGroups($viewer_id) {

    //GET GROUP TABLE AND ITS NAME
    $tableGroup = Engine_Api::_()->getDbTable('groups', 'sitegroup');
    $tableGroupName = $tableGroup->info('name');
    $tableClaimName = $this->info('name');
    //SELECT
    $select = $this->select()
            ->setIntegrityCheck(false)
            ->from($this->info('name'))
            ->joinInner($tableGroupName, "$tableClaimName.group_id = $tableGroupName.group_id", array('group_id', 'photo_id', 'title', 'owner_id'))
            ->where($tableClaimName . '.user_id = ?', $viewer_id)
            ->order('claim_id DESC');

    return Zend_Paginator::factory($select);
  }
}

?>