<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupmember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Menus.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitegroupmember_Plugin_Menus {

  public function canViewMembers() {

    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroupmember.member.show.menu', 1)) {
      return false;
    }
    $userstable = Engine_Api::_()->getDbtable('users', 'user');
    $userstableName = $userstable->info('name');
    
    $memberTable = Engine_Api::_()->getDbtable('membership', 'sitegroup');
    $membershipTableName = $memberTable->info('name');
    $groupTable = Engine_Api::_()->getDbtable('groups', 'sitegroup');
    $groupTableName = $groupTable->info('name');
    $select = $memberTable->select()
                    ->setIntegrityCheck(false)
                    ->from($groupTableName, array('COUNT(*) as count'))
                    ->join($membershipTableName, $membershipTableName . '.resource_id = ' . $groupTableName . '.group_id', array(''))
                    ->join($userstableName, $userstableName . '.user_id = ' . $membershipTableName . '.user_id', array(''))
                    ->where($membershipTableName .'.active = ?', 1)
                    ->where($groupTableName . '.closed = ?', '0')
                    ->where($groupTableName . '.approved = ?', '1')
                    ->where($groupTableName . '.search = ?', '1')
                    ->where($groupTableName . '.declined = ?', '0')
                    ->where($groupTableName . '.draft = ?', '1');
    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
      $select->where($groupTableName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
    } 
    $row = $select->query()->fetchColumn();
    //$count = count($row);
    if (empty($row)) {
      return false;
    }
    return true;
  }
  
  public function onMenuInitialize_SitegroupGutterJoin($row) {

    if (!Engine_Api::_()->core()->hasSubject('sitegroup_group')) {
      return false;
    }

    $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');
    if (empty($sitegroup->member_approval)) {
			return false;
    }
    
    $sitegroupmemberMenuActive = Zend_Registry::isRegistered('sitegroupmemberMenuActive') ? Zend_Registry::get('sitegroupmemberMenuActive') : null;
    if( empty($sitegroupmemberMenuActive) ) {
      return false;
    }
    
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    if ($viewer_id == $sitegroup->owner_id) {
			return false;
    }
    if (empty($viewer_id)) { 
			return false;
    }
    // PACKAGE BASE PRIYACY START
    $allowGroup = Engine_Api::_()->sitegroup()->allowInThisGroup($sitegroup, "sitegroupmember", 'smecreate');
    if (empty($allowGroup)) {
			return false;
		}
//     if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
//       if (!Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupmember")) {
//         return false;
//       }
//     } else {
//       $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'smecreate');
//       if (empty($isGroupOwnerAllow)) {
//         return false;
//       }
//     } 
    // PACKAGE BASE PRIYACY END

    $hasMembers = Engine_Api::_()->getDbTable('membership', 'sitegroup')->hasMembers($viewer_id, $sitegroup->group_id);

    if (!empty($hasMembers)) {
      return false;
    }

    // Modify params
    $params = $row->params;
    $params['params']['group_id'] = $sitegroup->getIdentity();
    return $params;
  }
  
  public function onMenuInitialize_SitegroupGutterLeave($row) {

    if (!Engine_Api::_()->core()->hasSubject('sitegroup_group')) {
      return false;
    }

    $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');
    
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    if (empty($viewer_id)) { 
			return false;
    }
    if ($viewer_id == $sitegroup->owner_id) {
			return false;
    }
    
    // PACKAGE BASE PRIYACY START
    $allowGroup = Engine_Api::_()->sitegroup()->allowInThisGroup($sitegroup, "sitegroupmember", 'smecreate');
    if (empty($allowGroup)) {
			return false;
		}
//     if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
//       if (!Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupmember")) {
//         return false;
//       }
//     } else {
//       $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'smecreate');
//       if (empty($isGroupOwnerAllow)) {
//         return false;
//       }
//     }
    // PACKAGE BASE PRIYACY END
    
    $sitegroupmemberMenuActive = Zend_Registry::isRegistered('sitegroupmemberMenuActive') ? Zend_Registry::get('sitegroupmemberMenuActive') : null;
    if( empty($sitegroupmemberMenuActive) ) {
      return false;
    }
    
    $isGroupAdmins = Engine_Api::_()->getDbtable('manageadmins', 'sitegroup')->isGroupAdmins($viewer_id, $sitegroup->getIdentity());
    if (!empty($isGroupAdmins)) {
			return false;
    }

		$hasMembers = Engine_Api::_()->getDbTable('membership', 'sitegroup')->hasMembers($viewer_id, $sitegroup->group_id, $params = "Leave");
		
    if (empty($hasMembers)) {
      return false;
    }

    // Modify params
    $params = $row->params;
    $params['params']['group_id'] = $sitegroup->getIdentity();
    return $params;
  }
  
  public function onMenuInitialize_SitegroupGutterRequest($row) {

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    if (empty($viewer_id)) { 
			return false;
    }
    if (!Engine_Api::_()->core()->hasSubject('sitegroup_group')) {
      return false;
    }

    $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');
    if (!empty($sitegroup->member_approval)) {
			return false;
    }
    
    $sitegroupmemberMenuActive = Zend_Registry::isRegistered('sitegroupmemberMenuActive') ? Zend_Registry::get('sitegroupmemberMenuActive') : null;
    if( empty($sitegroupmemberMenuActive) ) {
      return false;
    }
    
    if ($viewer_id == $sitegroup->owner_id) {
			return false;
    }
    
    // PACKAGE BASE PRIYACY START
    $allowGroup = Engine_Api::_()->sitegroup()->allowInThisGroup($sitegroup, "sitegroupmember", 'smecreate');
    if (empty($allowGroup)) {
			return false;
		}
//     if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
//       if (!Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupmember")) {
//         return false;
//       }
//     } else {
//       $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'smecreate');
//       if (empty($isGroupOwnerAllow)) {
//         return false;
//       }
//     }
    // PACKAGE BASE PRIYACY END

		$hasMembers = Engine_Api::_()->getDbTable('membership', 'sitegroup')->hasMembers($viewer_id, $sitegroup->group_id);
    if (!empty($hasMembers)) {
      return false;
    }

    // Modify params
    $params = $row->params;
    $params['params']['group_id'] = $sitegroup->getIdentity();
    return $params;
  }
  
  public function onMenuInitialize_SitegroupGutterCancel($row) {

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    if (empty($viewer_id)) { 
			return false;
    }
    
    if (!Engine_Api::_()->core()->hasSubject('sitegroup_group')) {
      return false;
    }

    $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');
    if (!empty($sitegroup->member_approval)) {
			return false;
    }
    
    $sitegroupmemberMenuActive = Zend_Registry::isRegistered('sitegroupmemberMenuActive') ? Zend_Registry::get('sitegroupmemberMenuActive') : null;
    if( empty($sitegroupmemberMenuActive) ) {
      return false;
    }

    if ($viewer_id == $sitegroup->owner_id) {
			return false;
    }

    // PACKAGE BASE PRIYACY START
    $allowGroup = Engine_Api::_()->sitegroup()->allowInThisGroup($sitegroup, "sitegroupmember", 'smecreate');
    if (empty($allowGroup)) {
			return false;
		}
//     if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
//       if (!Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupmember")) {
//         return false;
//       }
//     } else {
//       $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'smecreate');
//       if (empty($isGroupOwnerAllow)) {
//         return false;
//       }
//     }
    // PACKAGE BASE PRIYACY END

		$hasMembers = Engine_Api::_()->getDbTable('membership', 'sitegroup')->hasMembers($viewer_id, $sitegroup->group_id, $params = 'Cancel');
    if (empty($hasMembers)) {
      return false;
    }

    // Modify params
    $params = $row->params;
    $params['params']['group_id'] = $sitegroup->getIdentity();
    return $params;
  }
  
  public function onMenuInitialize_SitegroupGutterInvite($row) {

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    if (!Engine_Api::_()->core()->hasSubject('sitegroup_group')) {
      return false;
    }
    
    $sitegroupmemberMenuActive = Zend_Registry::isRegistered('sitegroupmemberMenuActive') ? Zend_Registry::get('sitegroupmemberMenuActive') : null;
    if( empty($sitegroupmemberMenuActive) ) {
      return false;
    }

    $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');
    
		//CHECK THAT WE HAVE TO SHOW MANAGE ADMIN LINK OR NOTE
    $groupAdmin = Engine_Api::_()->getDbTable('manageadmins', 'sitegroup')->isGroupAdmins($viewer_id, $sitegroup->getIdentity());
    if (!empty($groupAdmin)) {
			return false;
    }
    
    $hasMembers = Engine_Api::_()->getDbTable('membership', 'sitegroup')->hasMembers($viewer_id, $sitegroup->group_id, $params = 'Invite');
    if (empty($hasMembers)) {
      return false;
    }

    // PACKAGE BASE PRIYACY START
    $allowGroup = Engine_Api::_()->sitegroup()->allowInThisGroup($sitegroup, "sitegroupmember", 'smecreate');
    if (empty($allowGroup)) {
			return false;
		}
//     if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
//       if (!Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupmember")) {
//         return false;
//       }
//     } else {
//       $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'smecreate');
//       if (empty($isGroupOwnerAllow)) {
//         return false;
//       }
//     }
    // PACKAGE BASE PRIYACY END

    //START MANAGE-ADMIN CHECK
    if (!empty($sitegroup->member_invite)) {
      return false;
// 			$isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
// 			if (empty($isManageAdmin)) {
// 				return false;
// 			}
    }

    // Modify params
    $params = $row->params;
    $params['params']['group_id'] = $sitegroup->getIdentity();
    return $params;
  }
  
  public function onMenuInitialize_SitegroupGutterInviteGroupadmin($row) {

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    if (!Engine_Api::_()->core()->hasSubject('sitegroup_group')) {
      return false;
    }
    
    $sitegroupmemberMenuActive = Zend_Registry::isRegistered('sitegroupmemberMenuActive') ? Zend_Registry::get('sitegroupmemberMenuActive') : null;
    if( empty($sitegroupmemberMenuActive) ) {
      return false;
    }

    $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');
    
		//CHECK THAT WE HAVE TO SHOW MANAGE ADMIN LINK OR NOTE
    $groupAdmin = Engine_Api::_()->getDbTable('manageadmins', 'sitegroup')->isGroupAdmins($viewer_id, $sitegroup->getIdentity());
    if (empty($groupAdmin)) {
			return false;
    }
    
    $hasMembers = Engine_Api::_()->getDbTable('membership', 'sitegroup')->hasMembers($viewer_id, $sitegroup->group_id, $params = 'Invite');
    if (empty($hasMembers)) {
      return false;
    }
    
    $automaticallyJoin = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'groupmember.automatically.addmember', 1);
    if (empty($automaticallyJoin) && empty($sitegroup->member_approval)) {
			return false;
    }
    
    // PACKAGE BASE PRIYACY START
    $allowGroup = Engine_Api::_()->sitegroup()->allowInThisGroup($sitegroup, "sitegroupmember", 'smecreate');
    if (empty($allowGroup)) {
			return false;
		}
//     if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
//       if (!Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupmember")) {
//         return false;
//       }
//     } else {
//       $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'smecreate');
//       if (empty($isGroupOwnerAllow)) {
//         return false;
//       }
//     }
    // PACKAGE BASE PRIYACY END
    
    //START MANAGE-ADMIN CHECK
    if (!empty($sitegroup->member_invite)) {
			$isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
			if (empty($isManageAdmin)) {
				return false;
			}
    }

    // Modify params
    $params = $row->params;
    $params['params']['group_id'] = $sitegroup->getIdentity();
    return $params;
  }

  public function onMenuInitialize_SitegroupGutterRespondinvite($row) {

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    if (!Engine_Api::_()->core()->hasSubject('sitegroup_group')) {
      return false;
    }

    $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');
    if ($viewer_id == $sitegroup->owner_id) {
			return false;
    }
    
    $sitegroupmemberMenuActive = Zend_Registry::isRegistered('sitegroupmemberMenuActive') ? Zend_Registry::get('sitegroupmemberMenuActive') : null;
    if( empty($sitegroupmemberMenuActive) ) {
      return false;
    }

    // PACKAGE BASE PRIYACY START
    $allowGroup = Engine_Api::_()->sitegroup()->allowInThisGroup($sitegroup, "sitegroupmember", 'smecreate');
    if (empty($allowGroup)) {
			return false;
		}
//     if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
//       if (!Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupmember")) {
//         return false;
//       }
//     } else {
//       $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'smecreate');
//       if (empty($isGroupOwnerAllow)) {
//         return false;
//       }
//     }
    // PACKAGE BASE PRIYACY END
		
		$hasMembers = Engine_Api::_()->getDbTable('membership', 'sitegroup')->hasMembers($viewer_id, $sitegroup->group_id, $params = 'Accept');
    if (empty($hasMembers)) {
      return false;
    }

    $hasMembers = Engine_Api::_()->getDbTable('membership', 'sitegroup')->hasMembers($viewer_id, $sitegroup->group_id, $params = 'Reject');
    if (empty($hasMembers)  || empty($hasMembers->resource_approved)) {
      return false;
    }

    // Modify params
    $params = $row->params;
    $params['params']['group_id'] = $sitegroup->getIdentity();
    return $params;
  }
   	
	public function onMenuInitialize_SitegroupGutterRespondmemberinvite($row) {

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    if (!Engine_Api::_()->core()->hasSubject('sitegroup_group')) {
      return false;
    }

    $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');
    if ($viewer_id == $sitegroup->owner_id) {
			return false;
    }
    
    $sitegroupmemberMenuActive = Zend_Registry::isRegistered('sitegroupmemberMenuActive') ? Zend_Registry::get('sitegroupmemberMenuActive') : null;
    if( empty($sitegroupmemberMenuActive) ) {
      return false;
    }
    
    $automaticallyJoin = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'groupmember.automatically.addmember', 1);
    if (!empty($automaticallyJoin) && !empty($sitegroup->member_approval)) {
			return false;
    }
    
    // PACKAGE BASE PRIYACY START
    $allowGroup = Engine_Api::_()->sitegroup()->allowInThisGroup($sitegroup, "sitegroupmember", 'smecreate');
    if (empty($allowGroups)) {
			return false;
		}
    // PACKAGE BASE PRIYACY END
		
		$hasMembers = Engine_Api::_()->getDbTable('membership', 'sitegroup')->hasMembers($viewer_id, $sitegroup->group_id, $params = 'Accept');
    if (empty($hasMembers)) {
      return false;
    }

    $hasMembers = Engine_Api::_()->getDbTable('membership', 'sitegroup')->hasMembers($viewer_id, $sitegroup->group_id, $params = 'Reject');
    if (empty($hasMembers)  || empty($hasMembers->resource_approved)) {
      return false;
    }

    // Modify params
    $params = $row->params;
    $params['params']['group_id'] = $sitegroup->getIdentity();
    $params['params']['param'] = 'Invite';
    return $params;
  }
  
  	public function onMenuInitialize_SitegroupGutterManageJoinedMembers($row) {

		//GETTING THE VIEWER
		$viewer = Engine_Api::_()->user()->getViewer();
		
		$joineGroups = Engine_Api::_()->getDbtable('membership', 'sitegroup')->getJoinGroups($viewer->getIdentity(), 'groupJoin');
    if ($joineGroups->getTotalItemCount() == 0) {
      return false;
    }
    
		$coreContentTable = Engine_Api::_()->getDbtable('content', 'core');
		$coreContentTableName = $coreContentTable->info('name');

		$select = new Zend_Db_Select($coreContentTable->getAdapter());
		$select->from($coreContentTableName, 'content_id')->where("name = ?", 'sitegroup.profile-joined-sitegroup');
		$data = $select->query()->fetchColumn();
		if(empty($data)) {
			return false;
		}

		if ($viewer->getIdentity()) {
			//Return EDIT LINK
			return array(
				'label' => $row->label,
				'icon' => Zend_Registry::get('Zend_View')->layout()->staticBaseUrl.'application/modules/Sitegroup/externals/images/add_more_groups.png',
				'route' => 'user_profile',
				'params' => array(
					'id' => $viewer->getIdentity(),
					'tab' => $data
				)
			);
		}
	}

    public function sitegroupGutterNotificationSettings($row) {

        //RETURN FALSE IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('sitegroup_group')) {
            return false;
        }

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        //GET EVENT SUBJECT
        $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');

        $row = Engine_Api::_()->getDbTable('membership', 'sitegroup')->getRow($sitegroup, $viewer);

        if (!$row)
           return false;

        if(!$row->active || !$row->user_approved)
				  return false;

        return array(
            'class' => 'buttonlink icon_sitegroup_notification smoothbox',
            'route' => "sitegroupmember_approve",
            'action' => 'notification-settings',
            'params' => array(
                'member_id' => $row->member_id,
								'action' => 'notification-settings'
            ),
        );
    }
}
