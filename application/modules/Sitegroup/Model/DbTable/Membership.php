<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Membership.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Model_DbTable_Membership extends Core_Model_DbTable_Membership {

  protected $_type = 'sitegroup_group';

   /**
   * Get groupmembers list
   *
   * @param array $params
   * @param string $request_count
   * @return array $paginator;
   */
  public function getSitegroupmembersPaginator($params = array(), $request_count = null) {
 
    $paginator = Zend_Paginator::factory($this->getsitegroupmembersSelect($params, $request_count));
    if (!empty($params['group'])) {
      $paginator->setCurrentPageNumber($params['group']);
    }
    
    if (!empty($params['limit'])) {
      $paginator->setItemCountPerPage($params['limit']);
    }
     
    return $paginator;
  }

  /**
   * Get group member select query
   *
   * @param array $params
   * @param string $request_count
   * @return string $select;
   */
  public function getsitegroupmembersSelect($params = array(), $request_count = null) {

    $membershipTableName = $this->info('name');

    $usersTable = Engine_Api::_()->getDbtable('users', 'user');
    $usersTableName = $usersTable->info('name');

    $groupTable = Engine_Api::_()->getDbtable('groups', 'sitegroup');
    $groupTableName = $groupTable->info('name');

    $groupmanageadminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitegroup');
    $groupmanageadminsTableName = $groupmanageadminsTable->info('name');

    $select = $usersTable->select()
												->setIntegrityCheck(false)
												->from($usersTableName)
												->joinleft($membershipTableName, $usersTableName . ".user_id = " . $membershipTableName . '.user_id')
												->joinleft($groupTableName, $membershipTableName . ".group_id = " . $groupTableName . '.group_id', array('owner_id AS group_owner_id'))
												->where($membershipTableName . '.resource_id = ?', $params['group_id']);
					
		if ($request_count == 'request') {
			$select = $select->where($membershipTableName . '.active = ?', '0')
			                 ->where($membershipTableName . '.resource_approved = ?', '0')
			                 ->where($membershipTableName . '.user_approved = ?', '0');
		} else {
			$select = $select->where($membershipTableName . '.active = ?', '1')
					            ->where($membershipTableName . '.user_approved = ?', '1');
		}
		
		//GET THE FRIEND OF LOGIN USER.
		$friendId = Engine_Api::_()->user()->getViewer()->membership()->getMembershipsOfIds();
		if($request_count == 'friend') {			
			if(!empty($friendId)) {
				$select->where($membershipTableName . '.user_id IN (?)', (array) $friendId);
			}
		}
		
	  if($request_count == 'GROUPADMINS') {
			$select = $select->join($groupmanageadminsTableName, $membershipTableName . ".user_id = " . $groupmanageadminsTableName . '.user_id')
				->where($groupmanageadminsTableName . '.group_id = ?', $params['group_id']);
		}
		
		if ($request_count == 'otherMember' && !empty($friendId)) {
			$select->where($membershipTableName . '.user_id NOT IN (?)', (array) $friendId);
		}
		
		if(!empty($params['search'])) {
			$select = $select->where($usersTableName . ".displayname LIKE ? ", '%' . $params['search'] . '%');
		}

		if (isset($params['roles_id']) && $params['roles_id']) {
			$toDelete='-1';
			$params['roles_id'] = array_diff($params['roles_id'], array($toDelete));
			$roleIDs = @implode('","',$params['roles_id']);
			$select = $select->where($membershipTableName . '.role_id LIKE ?', '%' . $roleIDs . '%');
		}
		
		if (isset($params['category_roleid']) && !empty($params['category_roleid'])) {
			$select = $select->where($membershipTableName . '.role_id = ?', $params['category_roleid']);
		}
		
		if (isset($params['orderby']) && !empty($params['orderby'])) {
			if ($params['orderby'] == 'featured') {
				$select = $select->where($membershipTableName . '.featured = ?', '1');
			} elseif($params['orderby'] == 'highlighted') {
				$select = $select->where($membershipTableName . '.highlighted = ?', '1');
			} elseif($params['orderby'] == 'displayname') { 
				$select = $select->order($usersTableName . '.displayname ASC');
			} elseif($params['orderby'] == 'groupadmin') {
				$select = $select->join($groupmanageadminsTableName, $membershipTableName . ".user_id = " . $groupmanageadminsTableName . '.user_id', null)
				->where($groupmanageadminsTableName . '.group_id = ?', $params['group_id']);
			} elseif($params['orderby'] == 'myfriend') {
			  $user_id = Engine_Api::_()->user()->getViewer()->membership()->getMembershipsOfIds();
				$select = $select->where($membershipTableName . '.user_id IN (?)', (array) $user_id);
			}
		}

		$select = $select->order($membershipTableName . '.highlighted DESC')
			              ->order($membershipTableName . '.join_date DESC');
    return $select;
  }


  /**
   * Return member data
   *
   * @param array params
   * @return Zend_Db_Table_Select
   */
  public function widgetMembersData($params = array()) {

		$tableMemberName = $this->info('name');
		
		$tableGroup = Engine_Api::_()->getDbtable('groups', 'sitegroup');
		$tableGroupName = $tableGroup->info('name');
		
		$UserTable = Engine_Api::_()->getDbtable('users', 'user');
		$UserTableName = $UserTable->info('name');

		$select = $UserTable->select()
				->setIntegrityCheck(false)
				->from($UserTableName, array('user_id', 'username', 'displayname', 'photo_id'));
				
		if ($params['widget_name'] == 'recent')	 {
			$select->join($tableMemberName, $UserTableName . ".user_id = " . $tableMemberName . '.user_id', array('title AS groupmember_title','COUNT("user_id") AS JOINP_COUNT'))->limit($params['limit'])->group("$tableMemberName.user_id")->order($tableMemberName . '.join_date DESC');
		}
		
		if ($params['widget_name'] == 'featured') {
			$select->join($tableMemberName, $UserTableName . ".user_id = " . $tableMemberName . '.user_id', array('title AS groupmember_title', 'COUNT("user_id") AS JOINP_COUNT'))->where($tableMemberName . '.featured = ?', 1)->group("$tableMemberName.user_id")->limit($params['limit']);
		}
		
		if ($params['widget_name'] == 'mostvaluable') {
			$select->join($tableMemberName, $UserTableName . ".user_id = " . $tableMemberName . '.user_id', array('title AS groupmember_title', 'COUNT("user_id") AS JOINP_COUNT'))
			->group("$tableMemberName.user_id")->limit($params['limit']); 
		}
		
	  if ($params['widget_name'] == 'mostvaluablegroups') {
			$select->join($tableMemberName, $UserTableName . ".user_id = " . $tableMemberName . '.user_id', array('title AS groupmember_title', 'COUNT("group_id") AS MEMBER_COUNT'))
			->group("$tableMemberName.resource_id")->limit($params['limit']);
		}
		
    $select->join($tableGroupName, $tableMemberName . ".resource_id = " . $tableGroupName . '.group_id', array('title AS group_title', 'group_id', 'owner_id'))
					->where($tableMemberName . '.active = ?', 1)
					->where($tableGroupName . '.closed = ?', '0')
					->where($tableGroupName . '.approved = ?', '1')
					->where($tableGroupName . '.search = ?', '1')
					->where($tableGroupName . '.declined = ?', '0')
					->where($tableGroupName . '.draft = ?', '1');
					
		if ($params['widget_name'] == 'mostvaluablegroups') {
			$select->order('MEMBER_COUNT DESC');
		}
   
		if ($params['widget_name'] == 'mostvaluable') {
			$select->order('JOINP_COUNT DESC');
		}

		//End Network work
		return $UserTable->fetchAll($select);
  }
  
  /**
   * Return member of the day
   *
   * @return Zend_Db_Table_Select
   */
  public function memberOfDay() {

    //CURRENT DATE TIME
    $date = date('Y-m-d');

    //GET ITEM OF THE DAY TABLE NAME
    $memberOfTheDayTableName = Engine_Api::_()->getDbtable('itemofthedays', 'sitegroup')->info('name');

		//GET GROUP TABLE NAME
		$groupTableName = Engine_Api::_()->getDbtable('groups', 'sitegroup')->info('name');

    $UserTable = Engine_Api::_()->getDbtable('users', 'user');
		$userTableName = $UserTable->info('name');

		$groupMembershipTableName = $this->info('name');
		
    //MAKE QUERY
    $select = $UserTable->select()
												->setIntegrityCheck(false)
												->from($userTableName)
												->join($memberOfTheDayTableName, $userTableName . '.user_id = ' . $memberOfTheDayTableName . '.resource_id')
												->join($groupMembershipTableName, $userTableName . '.user_id = ' . $groupMembershipTableName . '.user_id', array(''))
												//->where($groupTableName.'.approved = ?', '1')
												//->where($groupTableName.'.declined = ?', '0')
												//->where($groupTableName.'.draft = ?', '1')
												->where($memberOfTheDayTableName . '.resource_type = ?', 'user')
												->where($memberOfTheDayTableName . '.start_date <= ?', $date)
												->where($memberOfTheDayTableName .'.end_date >= ?', $date)
												->order('Rand()');
    return $UserTable->fetchRow($select);
  }
  
  /**
   * Return group members
   *
   * @param array $params
   * @return Zend_Db_Table_Select
   */
  public function getMembers($params = array(), $showMember) {


		//GET THE FRIEND OF LOGIN USER.
		$friendId = Engine_Api::_()->user()->getViewer()->membership()->getMembershipsOfIds();
		
    //VIDEO TABLE NAME
    $membershipTableName = $this->info('name');
    
    $userTable = Engine_Api::_()->getDbtable('users', 'user');
    $userTableName = $userTable->info('name');

    //GROUP TABLE
    $groupTable = Engine_Api::_()->getDbtable('groups', 'sitegroup');
    $groupTableName = $groupTable->info('name');

    $groupPackagesTable = Engine_Api::_()->getDbtable('packages', 'sitegroup');
    $groupPackageTableName = $groupPackagesTable->info('name');

    //QUERY MAKING
    $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from($groupTableName, array('photo_id', 'title as sitegroup_title'))
                    ->join($membershipTableName, $membershipTableName . '.resource_id = ' . $groupTableName . '.group_id')
                    ->join($userTableName, $userTableName . '.user_id = ' . $membershipTableName . '.user_id', array('COUNT("user_id") AS JOINP_COUNT', 'displayname'))
                    ->join($groupPackageTableName, "$groupPackageTableName.package_id = $groupTableName.package_id",array('package_id', 'price'))
                    ->where($membershipTableName . '.active = ?', '1')
                    ->where($membershipTableName . '.user_approved = ?', '1');

		if ($showMember == 'friend' && !empty($friendId)) {
			$select->where($membershipTableName . '.user_id IN (?)', (array) $friendId);
		}
		
		if ($showMember == 'otherMember'  && !empty($friendId)) {
			$select->where($membershipTableName . '.user_id NOT IN (?)', (array) $friendId);
		}

    if (!empty($params['title'])) {
      $select->where($groupTableName . ".title LIKE ? ", '%' . $params['title'] . '%');
    }

		$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

		if ((isset($params['orderby']) && $params['orderby'] == 'join_date') || !empty($params['viewedvideo'])) {
			$select = $select
											->order($membershipTableName .'.join_date DESC');
											//->order($membershipTableName .'.creation_date DESC');
		} elseif ((isset($params['orderby']) && $params['orderby'] == 'featured_member') || !empty($params['commentedvideo'])) {
			$select = $select->where($membershipTableName . '.featured = ?', '1')
											->order($membershipTableName .'.featured DESC')
											->order($membershipTableName .'.join_date DESC');
		}
		elseif ((isset($params['orderby']) && $params['orderby'] == 'member_count')) {
			$select = $select->order('JOINP_COUNT DESC');
		}
		
    if (!empty($params['search_member'])) {
				$select->where($userTableName . ".displayname LIKE ? OR " . $userTableName . ".username LIKE ?", '%' . $params['search_member'] . '%');
    }

		
    if (!empty($params['category'])) {
      $select->where($groupTableName . '.category_id = ?', $params['category']);
    }

    if (!empty($params['category_id'])) {
      $select->where($groupTableName . '.category_id = ?', $params['category_id']);
    }

		if (!empty($params['subcategory'])) {
      $select->where($groupTableName . '.subcategory_id = ?', $params['subcategory']);
    }

    if (!empty($params['subcategory_id'])) {
      $select->where($groupTableName . '.subcategory_id = ?', $params['subcategory_id']);
    }

    if (!empty($params['subsubcategory'])) {
      $select->where($groupTableName . '.subsubcategory_id = ?', $params['subsubcategory']);
    }

    if (!empty($params['subsubcategory_id'])) {
      $select->where($groupTableName . '.subsubcategory_id = ?', $params['subsubcategory_id']);
    }

    
    $select = $select->where($groupTableName . '.closed = ?', '0')
                    ->where($groupTableName . '.approved = ?', '1')
                    ->where($groupTableName . '.search = ?', '1')
                    ->where($groupTableName . '.declined = ?', '0')
                    ->where($groupTableName . '.draft = ?', '1');

    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
      $select->where($groupTableName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
    }
    $select->group($membershipTableName . '.user_id');

    return Zend_Paginator::factory($select);
  }
  
  /**
   * Return member object
   *
   * @param int $memberId
   * @return Zend_Db_Table_Select
   */
  public function getMembersObject($memberId) {
  
    $membershipTableName = $this->info('name');
    $select = $this->select()
              ->where($membershipTableName . '.member_id = ?', $memberId);
    return $this->fetchRow($select);
  }
  
  /**
   * Return joined groups
   *
   * @param int $user_id
   * @param string $params
   * @return Zend_Db_Table_Select and paginator
   */
  public function getJoinGroups($user_id, $params) {

    $admins = '';
    $manageAdminsIds = Engine_Api::_()->getDbtable('manageadmins', 'sitegroup')->getManageAdminGroups($user_id);
    if(!empty($manageAdminsIds)) {
        $manageAdminsIdsArray = $manageAdminsIds->toArray();
        if($manageAdminsIdsArray) {
            foreach($manageAdminsIdsArray as $gadmins) {
                $admins .= "'". $gadmins['group_id'] . "'" . ', ';
            }
            $admins = '(' . trim($admins, ", ") . ')';
        }
    }
    
		$tableMemberName = $this->info('name');

		$tableGroup = Engine_Api::_()->getDbtable('groups', 'sitegroup');
		$tableGroupName = $tableGroup->info('name');

		$select = $tableGroup->select()
												->setIntegrityCheck(false)
												->from($tableGroupName, array('group_id','title','group_url', 'body', 'owner_id','photo_id','price','creation_date','featured','sponsored','view_count','comment_count','like_count','closed'))
												->joinleft($tableMemberName, $tableGroupName . ".group_id = " . $tableMemberName . '.resource_id', null)
												->where($tableMemberName . '.active = ?', 1)
												->where($tableMemberName . '.user_id = ?', $user_id)
												->where($tableGroupName . '.approved = ?', '1')
												->where($tableGroupName . '.draft = ?', '1')
												->where($tableGroupName . '.search = ?', '1')
												->where($tableGroupName . '.closed = ?', '0')
												->where($tableGroupName . '.declined = ?', '0');
												
//		if ($params == 'onlymember') {
//			$select->where($tableGroupName . '.owner_id <> ?', $user_id);
//		}
    
   
		if ($params == 'memberOfDay') {
			$result = $tableGroup->fetchAll($select);
		} elseif ($params == 'groupJoin') {
      $select->where($tableGroupName . '.owner_id <> ?', $user_id);  
			$result = Zend_Paginator::factory($select);
		} elseif($params == 'onlymember') {
         if(!empty($admins)) {
            $select->where($tableGroupName . '.group_id not in ?', new Zend_Db_Expr(trim($admins, ", ")));  
        }
        $select->where($tableGroupName . '.owner_id <> ?', $user_id);  
			  $result = $tableGroup->fetchAll($select);
    }
    
    
    
    
		return $result;
  }
  
  /**
   * Check member is join.
   *
   * @param int $viewer_id
   * @param int $group_id
   * @param string $params
   * @return Zend_Db_Table_Select and paginator
   */
  public function hasMembers($viewer_id, $group_id = NULL, $params = NULL) {

    $membershipTableName = $this->info('name');

    $select = $this->select()
						->from($membershipTableName)
						->where('user_id = ?', $viewer_id);
						
		if (!empty($group_id)) {
			$select->where($membershipTableName . '.resource_id = ?', $group_id);
		}
		
		if ($params == 'Leave') {
			$select->where('active = ?', 1);
		}
		
		if ($params == 'Cancel' || $params == 'Accept' || $params == 'Reject') {
			$select->where('active = ?', 0);
		}
		if($params == 'Cancel') {
		$select->where('resource_approved = ?', 0)
		      ->where('user_approved = ?', 0);
		}
		
	  if ($params == 'Accept') {
			$select->where('resource_approved = ?', 1);
		}
		
		if ($params == 'Invite') {
			$select->where('resource_approved = ?', 1)
							->where('user_approved = ?', 1)
							->where('active = ?', 1);
	  }
		$select = $this->fetchRow($select);

    return $select;
	}
	
	/**
   * Return join members
   *
   * @param int $group_id
   * @param int $viewer_id
   * @param int $ownerId
   * @return Zend_Db_Table_Select
   */
  public function getJoinMembers($group_id, $viewer_id = null, $ownerId = null, $onlyMemberWithPhoto = 0, $temp = null) {

		$tableMemberName = $this->info('name');
    $userTable = Engine_Api::_()->getDbtable('users', 'user');
    $userTableName = $userTable->info('name');
		$select = $this->select()
							->setIntegrityCheck(false)
							->from($tableMemberName, array('user_id', 'email AS email_notification', 'action_email'))
							->join($userTableName, $userTableName . '.user_id = ' . $tableMemberName . '.user_id')
							->where($tableMemberName . '.active = ?', 1)
							->where($tableMemberName . '.resource_approved = ?', 1)
							->where($tableMemberName . '.user_approved = ?', 1)
							->where($tableMemberName . '.group_id = ?', $group_id);
							
		if (!empty($viewer_id))  {
			$select->where($tableMemberName . '.user_id <> ?', $ownerId)
			       ->where($tableMemberName . '.user_id <> ?', $viewer_id);
		}
		
    if($onlyMemberWithPhoto) {
      $select->where($userTableName . '.photo_id <> ?', 0);
    }
		
		$select->order($tableMemberName . '.join_date DESC');
		if(empty($temp)) {
			$result = Zend_Paginator::factory($select);
		} else {
			$result = $this->fetchAll($select);
		}

		return $result;
  }
  
  	/**
   * Return count groups
   *
   * @param int $user_id
   * @return count
   */
  public function countGroups($user_id) {

    $tableMemberName = $this->info('name');
		$select = $this->select()
									->from($tableMemberName, new Zend_Db_Expr('COUNT(*)'))
									->where($tableMemberName . '.active = ?', 1)
									->where('user_id = ?', $user_id);

		return	(integer) $select->query()->fetchColumn();
  }
  
  	/**
   * Return count groups
   *
   * @param int $user_id
   * @return count
   */
  public function listMemeberTabWidget($activTab) {
  
    $table = Engine_Api::_()->getDbtable('membership', 'sitegroup');
    $tableMembershipName = $table->info('name');

    $tableGroup = Engine_Api::_()->getDbtable('groups', 'sitegroup'); 
    $tableGroupName = $tableGroup->info('name');

    $userTable = Engine_Api::_()->getDbtable('users', 'user'); 
    $userTableName = $userTable->info('name');


		$select = $userTable->select()
			->setIntegrityCheck(false)
			->from($userTableName, array('user_id', 'username', 'displayname', 'photo_id'))
			->join($tableMembershipName, $userTableName . ".user_id = " . $tableMembershipName . '.user_id', array('COUNT("user_id") AS JOINP_COUNT'))
			->join($tableGroupName, $tableMembershipName . ".resource_id = " . $tableGroupName . '.group_id', array('title AS sitegroup_title', 'group_id', 'owner_id'))
			->where($tableMembershipName . '.active = ?', 1)
			->where($tableGroupName . '.closed = ?', '0')
			->where($tableGroupName . '.approved = ?', '1')
			->where($tableGroupName . '.search = ?', '1')
			->where($tableGroupName . '.declined = ?', '0')
			->where($tableGroupName . '.draft = ?', '1');
    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
      $select->where($tableGroupName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
    }
    switch ($activTab->name) {
      case 'recent_groupmembers':
       $select->order($tableMembershipName . '.join_date DESC');
      break;
      case 'featured_groupmembers':
        $select->where($tableMembershipName .'.featured = ?', 1);
        $select->order('Rand()');
      break;
    }

		$select->group("$tableMembershipName.user_id");

		return Zend_Paginator::factory($select);
  }

  public function userRoles($params = array()) {

    return $this->select()
            ->from($this->info('name'), 'title')
            ->where('group_id =?', $params['group_id'])
            ->where('active = ?', 1)
            ->where('user_id =?', $params['user_id'])
            ->query()
            ->fetchColumn();
  }
     
  public function notificationSettings($params = array()) {

    $tableMemberName = $this->info('name');
		$select = $this->select()
									->from($tableMemberName, $params['columnName'])
									->where($tableMemberName . '.user_id = ?', $params['user_id'])
									->where('group_id = ?', $params['group_id']);
		return $select->query()->fetchColumn();
  }
}