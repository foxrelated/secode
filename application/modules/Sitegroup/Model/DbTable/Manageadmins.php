<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Manageadmins.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Model_DbTable_Manageadmins extends Engine_Db_Table {

  protected $_rowClass = "Sitegroup_Model_Manageadmin";

	/**
   * Return manage admins
   *
   * @param int $group_id
   */
  public function getManageAdminUser($group_id) {

    $usertable = Engine_Api::_()->getDbtable('users', 'user');
    $userName = $usertable->info('name');
    $manageName = $this->info('name');
    $manageHistoriesQuery = $this->select()
            ->setIntegrityCheck(false)
            ->from($manageName)
            ->joinleft($userName, $manageName . '.group_id = ' . $userName . '.user_id', array('displayname', 'photo_id'))
            ->where('group_id = ?', $group_id);
    return Zend_Paginator::factory($manageHistoriesQuery);
  }

	/**
   * Return manage admin ids
   *
   * @param int $group_id
   */
	public function getManageAdmin($group_id, $user_id = null) {

    $select = $this->select()
                    ->from($this->info('name'))
                    ->where('group_id = ?', $group_id);
    if (!empty($user_id)) {
			$select->where('user_id <> ?', $user_id);
    }
    return $this->fetchAll($select);
	}

	/**
   * Return manage admin groups_id
   *
   * @param int $viewer_id
   */
	public function getManageAdminGroups($viewer_id) {

		$select = $this->select()
						->from($this->info('name'), 'group_id')
						->where('user_id = ?', $viewer_id);
		return $this->fetchAll($select);
	}

	/**
   * Return linked groups result
   *
   * @param int $group_id
   */
  public function linkedGroups($group_id) {

    $manageAdminName = $this->info('name');
    $select = $this->select()->from($manageAdminName, 'user_id')->where('group_id = ?', $group_id);
    return $user_idarray = $this->fetchAll($select)->toArray();
	}

	/**
   * Return featured manage admins
   *
   * @param int $group_id
   */
	public function featuredAdmins($group_id) {

		$select = $this->select()
						->where('featured = ?', 1)
						->where('group_id = ?', $group_id);

		if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
      return Zend_Paginator::factory($select);
    }

		return $this->fetchAll($select);
	}

	/**
   * Return group admin.
   *
   * @param int $user_id
   * @param int $group_id
   */
	public function isGroupAdmins($user_id, $group_id) {

    $select = $this->select()
						->from($this->info('name'))
						->where('user_id = ?', $user_id)
						->where('group_id = ?', $group_id);
    return $this->fetchRow($select);
	}
	
	/**
   * Return manage admin ids array
   *
   * @param int $group_id
   */
	public function getManageAdminIds($group_id, $param = null) {

		$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $select = $this->select()
							->from($this->info('name'), array('user_id'));
		if(!empty($param) && $param == 'groupintergration') { 
			$select->where('user_id <> ?', $viewer_id);
		}
		$user_ids = $select->where('group_id = ?', $group_id)
											->query()
											->fetchAll(Zend_Db::FETCH_COLUMN);
    return $user_ids;
	}
	
  public function getCountUserAsAdmin($params = array()) {
 
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    $groupTable = Engine_Api::_()->getDbTable('groups', 'sitegroup');
    $groupTableName = $groupTable->info('name');
    
    $manageAdminTableName = $this->info('name');

    $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from($manageAdminTableName, array("COUNT(engine4_sitegroup_manageadmins.group_id) AS adminCount"))
                    ->joinLeft($groupTableName, "$groupTableName.group_id = $manageAdminTableName.group_id", array())
       ->where("$manageAdminTableName.user_id = ?", $viewer_id)
       ->where("$groupTableName.owner_id != ?", $viewer_id)     
       ->group("$groupTableName.group_id");

    return $select->query()->fetchColumn();
  }
}
?>