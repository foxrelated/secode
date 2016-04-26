<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupmember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: MemberController.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitegroupmember_MemberController extends Seaocore_Controller_Action_Standard {

  //ACTION FOR MEMBER JOIN THE GROUP.
  public function joinAction() {

    //CHECK AUTH
    if( !$this->_helper->requireUser()->isValid() ) return;

    //SOMMTHBOX
    $this->_helper->layout->setLayout('default-simple');

    //MAKE FORM
    if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
			$this->view->form = $form = new Sitegroupmember_Form_Join();
    } else {
      $this->view->form = $form = new Sitegroupmember_Form_SitemobileJoin();
    }

    //IF THE MODE IS APP MODE THEN
    if (Engine_Api::_()->seaocore()->isSitemobileApp()) {
      $this->view->sitemapPageHeaderTitle = "Join Group";
      $form->setTitle('');
    }
    
		$viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();
		
		$group_id = $this->_getParam('group_id');
		$sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
		$owner = $sitegroup->getOwner();
    $action_notification=array();
	  	$notificationSettings = Engine_Api::_()->getDbTable('membership', 'sitegroup')->notificationSettings(array('user_id' => $sitegroup->owner_id, 'group_id' => $group_id, 'columnName' => array('action_notification')));
	  	if($notificationSettings)
	  	$action_notification = Zend_Json_Decoder::decode($notificationSettings);

    $hasMembers = Engine_Api::_()->getDbTable('membership', 'sitegroup')->hasMembers($viewer_id, $group_id);
		
    //IF MEMBER IS ALREADY PART OF THE GROUP
    if(!empty($hasMembers)) {
      return $this->_forwardCustom('success', 'utility', 'core', array(
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('You have already sent a membership request.')),
        'layout' => 'default-simple',
        'parentRefresh' => true,
      ));
    }

    //PROCESS FORM
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )	{

      
			//SET THE REQUEST AS HANDLED FOR NOTIFACTION.
			$friendId = Engine_Api::_()->user()->getViewer()->membership()->getMembershipsOfIds();
			if($action_notification && $action_notification['notificationjoin'] == 1) {
				Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($owner, $viewer, $sitegroup, 'sitegroup_join');
			} elseif($action_notification && in_array($sitegroup->owner_id, $friendId) && $action_notification['notificationjoin'] == 2) {
				Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($owner, $viewer, $sitegroup, 'sitegroup_join');
			}
			
			//ADD ACTIVITY
			$action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $sitegroup, 'sitegroup_join');
				if ( $action ) {
					Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity( $action , $sitegroup ) ;
				}
			Engine_Api::_()->getApi('subCore', 'sitegroup')->deleteFeedStream($action,true);
	

			//GET VALUE FROM THE FORM.
			$values = $this->getRequest()->getPost();

			$membersTable = Engine_Api::_()->getDbtable('membership', 'sitegroup');
			$row = $membersTable->createRow();
			$row->resource_id = $group_id;
			$row->group_id = $group_id;
			$row->user_id = $viewer_id;

        //FOR CATEGORY WORK.
				if (isset($values['role_id'])) {
					$roleName = array();
					foreach($values['role_id'] as $role_id) {
						$roleName[] = Engine_Api::_()->getDbtable('roles', 'sitegroupmember')->getRoleName($role_id);
					}
					$roleTitle = json_encode($roleName);
					$roleIDs = json_encode($values['role_id']);
					if ($roleTitle && $roleIDs) {
            $row->title = $roleTitle;
            $row->role_id = $roleIDs;
          }
				}

			//FOR DATE WORK.
			if (!empty($values['year']) || !empty($values['month']) || !empty($values['day'])) {
				$member_date = $values['year'] . '-' . (int) $values['month'] . '-' . (int) $values['day'];
				$row->date = $member_date;
			}

			//IF MEMBER IS ALREADY FEATURED THEN AUTOMATICALLY FEATURED WHEN MEMBER JOIN ANY GROUP.
			$sitegroupmember = Engine_Api::_()->getDbTable('membership', 'sitegroup')->hasMembers($viewer_id);
			if(!empty($sitegroupmember->featured) && $sitegroupmember->featured == 1) {
				$row->featured = 1;
			}

			$row->save();

			//MEMBER COUNT INCREASE WHEN MEMBER JOIN THE GROUP.
      Engine_Api::_()->sitegroup()->updateMemberCount($sitegroup);
			$sitegroup->save();

			//AUTOMATICALLY LIKE THE GROUP WHEN MEMBER JOIN THE GROUP.
			$autoLike = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'groupmember.automatically.like' , 0);
			if(!empty($autoLike)) {
				Engine_Api::_()->sitegroup()->autoLike($group_id, 'sitegroup_group');
			}
        
        //START DISCUSSION WORK WHEN MEMBER JOIN THE GROUP THEN ALL DISCUSSION IS WATCHABLE FOR JOINED MEMBERS.
        if(Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled('sitegroupdiscussion')) {
					$results = Engine_Api::_()->getDbTable('topics', 'sitegroup')->getGroupTopics($group_id);
					if(!empty($results)) {
						foreach($results as $result) {
						
						$topic_id = $result->topic_id;
						
						$db = Engine_Db_Table::getDefaultAdapter();
						
						$db->query("INSERT IGNORE INTO `engine4_sitegroup_topicwatches` (`resource_id`, `topic_id`, `user_id`, `watch`, `group_id`) VALUES ('$group_id', '$topic_id', '$viewer_id', '1', '$group_id');");
						}
					}
        }
        //END DISCUSSION WORK WHEN MEMBER JOIN THE GROUP THEN ALL DISCUSSION IS WATCHABLE FOR JOINED MEMBERS.
    
      return $this->_forwardCustom('success', 'utility', 'core', array(
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('You are now a member of this group.')),
        'layout' => 'default-simple',
        'parentRefresh' => true,
      ));
    }
  }

  //ACTION FOR LEAVE THE GROUP.
  public function leaveAction() {

    //CHECK AUTH
    if( !$this->_helper->requireUser()->isValid()) return;

		$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

		//GET GROUP ID.
		$group_id = $this->_getParam('group_id');
		$sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
		
    //MAKE FORM
    $this->view->form = $form = new Sitegroupmember_Form_Member();
    $form->setTitle('Leave Group');
    $form->setDescription('Are you sure you want to leave this group?');
    $form->submit->setLabel('Leave Group');
    
    //IF THE MODE IS APP MODE THEN
    if (Engine_Api::_()->seaocore()->isSitemobileApp()) {
      $this->view->sitemapPageHeaderTitle = "Leave Group";
      $form->setTitle('');
    }

    //PROCESS FORM
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )	{

      if (!empty($group_id)) {

        //DELETE THE RESULT FORM THE TABLE.
        Engine_Api::_()->getDbtable('membership', 'sitegroup')->delete(array('resource_id =?' => $group_id, 'user_id = ?' => $viewer_id));

        //DELETE ACTIVITY FEED OF JOIN GROUP ACCORDING TO USER ID.
        $action_id = Engine_Api::_()->getDbtable('actions', 'activity')->fetchRow(array('type = ?'  => 'sitegroup_join', 'subject_id = ?' => $viewer_id, 'object_id = ?' => $group_id));
        $action = Engine_Api::_()->getItem('activity_action', $action_id->action_id);
        if (!empty($action)) {
					$action->delete();
        }

				//REMOVE THE NOTIFICATION.
				$notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType($sitegroup->getOwner(), $sitegroup, 'sitegroup_join');
				if($notification) {
					$notification->delete();
				}
			
				//MEMBER COUNT DECREASE IN THE GROUP TABLE WHEN MEMBER LEAVE THE GROUP.
				$sitegroup->member_count--;
				$sitegroup->save();
      }

      return $this->_forwardCustom('success', 'utility', 'core', array(
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('You have successfully left this group.')),
        'layout' => 'default-simple',
        
        'parentRefresh' => true,
      ));
    }
  }

  //ACTION FOR REQUEST GROUP.
  public function requestAction() {

    //CHECK AUTH
    if( !$this->_helper->requireUser()->isValid() ) return;

    //SOMMTHBOX
    $this->_helper->layout->setLayout('default-simple');
    
    //MAKE FORM
    $this->view->form = $form = new Sitegroupmember_Form_Member();
    $form->setTitle('Request Group Membership');
    $form->setDescription('Would you like to request membership in this group?');
    $form->submit->setLabel('Send Request');
    
		//GET THE VIEWER ID.
		$viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();

		//GET THE GROUP ID.
		$group_id = $this->_getParam('group_id');
		$sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
		
		$grouptitle = $sitegroup->title;
		$group_url = Engine_Api::_()->sitegroup()->getGroupUrl($group_id);
		
		$group_baseurl = ((!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://":"http://") . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('group_url' => $group_url), 'sitegroup_entry_view', true);		
		$group_title_link = '<a href="' . $group_baseurl . '"  >' . $grouptitle . ' </a>';
			
    $hasMembers = Engine_Api::_()->getDbTable('membership', 'sitegroup')->hasMembers($viewer_id, $group_id);

    //IF MEMBER IS ALREADY PART OF THE GROUP
    if(!empty($hasMembers)) {
      return $this->_forwardCustom('success', 'utility', 'core', array(
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('You have already sent a membership request.')),
        'layout' => 'default-simple',
        'parentRefresh' => true,
      ));
    }

    //PROCESS FORM
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {

			$sitegroupmember = Engine_Api::_()->getDbTable('membership', 'sitegroup')->hasMembers($viewer_id);

      //GET MANAGE ADMIN AND SEND NOTIFICATIONS TO ALL MANAGE ADMINS.
			$manageadmins = Engine_Api::_()->getDbtable('manageadmins', 'sitegroup')->getManageAdmin($group_id);
		
			$values = $this->getRequest()->getPost();

			$membersTable = Engine_Api::_()->getDbtable('membership', 'sitegroup');

			$row = $membersTable->createRow();
			$row->resource_id = $group_id;
			$row->group_id = $group_id;
			$row->user_id = $viewer_id;
			$row->active = 0;
			$row->resource_approved = 0;
			$row->user_approved = 0;

			if (!empty($sitegroupmember->featured) && $sitegroupmember->featured == 1) {
				$row->featured = 1;
			}

			$row->save();
      
      
			foreach($manageadmins as $manageadmin) {

				$user_subject = Engine_Api::_()->user()->getUser($manageadmin['user_id']);

				Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user_subject, $viewer, $sitegroup, 'sitegroupmember_approve', array('member_id' => $row->member_id));

				//Email to all group admins.
				Engine_Api::_()->getApi('mail', 'core')->sendSystem($user_subject->email, 'SITEGROUPMEMBER_REQUEST_EMAIL', array(
						'group_title' => $grouptitle,
						'group_title_with_link' => $group_title_link,
						'object_link' => $group_baseurl,
						//'email' => $email,
						'queue' => true
				));
			}
      
      
			return $this->_forwardCustom('success', 'utility', 'core', array(
				'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your group membership request has been sent successfully.')),
				'layout' => 'default-simple',
				'parentRefresh' => true,
			));
    }
  }

  //ACTION FOR CANCEL MEMBER REQUEST.
  public function cancelAction() {

    //CHECK AUTH
    if( !$this->_helper->requireUser()->isValid() ) return;

		//GET GROUP ID.
		$group_id = $this->_getParam('group_id');
		$sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
		
		//GET VIEWER ID.
		$viewer = Engine_Api::_()->user()->getViewer();

    //MAKE FORM
    $this->view->form = $form = new Sitegroupmember_Form_Member();
    $form->setTitle('Cancel Group Membership Request');
    $form->setDescription('Would you like to cancel your request for membership in this group?');
    $form->submit->setLabel('Cancel Request');

    //PROCESS FORM
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {

			//REMOVE THE NOTIFICATION.
			$notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType($sitegroup->getOwner(), $sitegroup, 'sitegroupmember_approve');
			if( $notification ) {
				$notification->delete();
			}

			if (!empty($group_id)) {
				//DELETE THE RESULT FORM THE TABLE.
				Engine_Api::_()->getDbtable('membership', 'sitegroup')->delete(array('resource_id =?' => $group_id, 'user_id =?' => $viewer->getIdentity()));
			}

      return $this->_forwardCustom('success', 'utility', 'core', array(
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Group membership request cancelled.')),
        'layout' => 'default-simple',
        
        'parentRefresh' => true,
      ));
    }
  }

  //RESPOND REQUEST.
  public function respondAction() {
  
    // CHECK AUTH
    if( !$this->_helper->requireUser()->isValid() ) return;

    $viewer = Engine_Api::_()->user()->getViewer();

    //GET THE SITEGROUP ID FROM THE URL
    $group_id = $this->_getParam('group_id');
    $param = $this->_getParam('param');
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);

    $this->view->form = $form = new Sitegroupmember_Form_Respond();
    if ($param != 'Invite') {
			$form->setTitle('Respond to Membership Request');
			//$form->setDescription('Respond to Membership Request.');
    } else {
			$form->setTitle('Respond to Membership Invitation');
			//$form->setDescription('Respond to Membership Invitation.');
    }

      // Process form
    if( !$this->getRequest()->isPost() ) {
      $this->view->status = false;
      $this->view->error = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Invalid Method');
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      $this->view->status = false;
      $this->view->error = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Invalid Data');
      return;
    }
  
  		//GET VALUE FROM THE FORM.
		$values = $this->getRequest()->getPost(); 

		if (isset($values['accept'])) {
			
      if (!empty($sitegroup->member_approval)) {
				//ADD ACTIVITY.
				$action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $sitegroup, 'sitegroup_join');
				if ( $action ) {
					Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity( $action , $sitegroup ) ;
				}
				Engine_Api::_()->getApi('subCore', 'sitegroup')->deleteFeedStream($action,true);
				Engine_Api::_()->getDbtable('membership', 'sitegroup')->update(array('active'=>  '1', 'user_approved' => '1'), array('resource_id =?' => $group_id, 'user_id =?' => $viewer->getIdentity()));
				
				//MEMBER COUNT INCREASE WHEN MEMBER JOIN THE GROUP.
        Engine_Api::_()->sitegroup()->updateMemberCount($sitegroup);
				$sitegroup->save();
			} 
			else {
				Engine_Api::_()->getDbtable('membership', 'sitegroup')->update(array('active'=>  '0', 'user_approved' => '0', 'resource_approved' => '0'), array('resource_id =?' => $group_id, 'user_id =?' => $viewer->getIdentity()));
			
				//GET MANAGE ADMIN AND SEND NOTIFICATIONS TO ALL MANAGE ADMINS.
				$manageadmins = Engine_Api::_()->getDbtable('manageadmins', 'sitegroup')->getManageAdmin($group_id);
				foreach($manageadmins as $manageadmin) {

					$user_subject = Engine_Api::_()->user()->getUser($manageadmin['user_id']);
                                        $row = Engine_Api::_()->getDbTable('membership', 'sitegroup')->getRow($sitegroup, $viewer);
					Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user_subject, $viewer, $sitegroup, 'sitegroupmember_approve', array('member_id' => $row->member_id));
				}
			}
		} 
		else {
			if (!empty($group_id)) {
				//DELETE THE RESULT FORM THE TABLE.
				Engine_Api::_()->getDbtable('membership', 'sitegroup')->delete(array('resource_id =?' => $group_id, 'user_id =?' => $viewer->getIdentity()));
			}
		}
    $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType($viewer, $sitegroup, 'sitegroupmember_invite');
 			if( $notification ) {
 				$notification->mitigated = true;
                                $notification->read = true;
 				$notification->save();
 			}
    $this->view->status = true;
    $this->view->error = false;
    if (isset($values['accept']) && !empty($automaticallyJoin) && !empty($sitegroup->member_approval)) {
			$message = Zend_Registry::get('Zend_Translate')->_('You have accepted the invite to the group %s.');
    }  elseif (isset($values['accept']) && empty($automaticallyJoin) && empty($sitegroup->member_approval)) {
			$message = Zend_Registry::get('Zend_Translate')->_('You have accepted the invitation to join the group %s.');
    } elseif (isset($values['accept']) && empty($automaticallyJoin) && !empty($sitegroup->member_approval)) {
			$message = Zend_Registry::get('Zend_Translate')->_('You have accepted the invitation to join the group %s.');
    } 
    else {
			$message = Zend_Registry::get('Zend_Translate')->_('You have ignored the invite to the group %s.');
    }
    
    $message = sprintf($message, $sitegroup->__toString());
    $this->view->message = $message;

    if( null === $this->_helper->contextSwitch->getCurrentContext() ) {
      return $this->_forwardCustom('success', 'utility', 'core', array(
        'messages' => array($message),
        'layout' => 'default-simple',
        'parentRefresh' => true,
      ));
    }
  }
  
  //ACTION FOR ACCEPT GROUP MEMBER REQUEST.
  public function acceptAction() {

    //CHECK AUTH
    if( !$this->_helper->requireUser()->isValid() ) return;

    $viewer = Engine_Api::_()->user()->getViewer();

    //GET THE SITEGROUP ID FROM THE URL
    $group_id = $this->_getParam('group_id');
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
    
    //MAKE FORM
    $this->view->form = $form = new Sitegroupmember_Form_Member();
    $form->setTitle('Accept Group Invitation');
    $form->setDescription('Would you like to accept group invitation for this group?');
    $form->submit->setLabel('Accept Group Invitation');

    //PROCESS FORM
    if( !$this->getRequest()->isPost() ) {
      $this->view->status = false;
      $this->view->error = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Invalid Method');
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      $this->view->status = false;
      $this->view->error = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Invalid Data');
      return;
    }

		//SET THE REQUEST AS HANDLED
		$notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType($viewer, $sitegroup, 'sitegroupmember_invite');
		if( $notification ) {
			$notification->mitigated = true;
      $notification->read = true;
			$notification->save();
		}

		//GET VALUE FROM THE FORM.
		$values = $this->getRequest()->getPost();
		
		//ADD ACTIVITY
    $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $sitegroup, 'sitegroup_join');
    Engine_Api::_()->getApi('subCore', 'sitegroup')->deleteFeedStream($action,true);

		Engine_Api::_()->getDbtable('membership', 'sitegroup')->update(array('active'=>  '1', 'user_approved' => '1'), array('resource_id =?' => $group_id, 'user_id =?' => $viewer->getIdentity()));
		
		//MEMBER COUNT INCREASE WHEN MEMBER JOIN THE GROUP.
    Engine_Api::_()->sitegroup()->updateMemberCount($sitegroup);
		$sitegroup->save();
		
    $this->view->status = true;
    $this->view->error = false;
    
   
    // Set the request as handled
    $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
            $viewer, $sitegroup, 'sitegroupmember_approve');
    if ($notification) {
        $notification->mitigated = true;
        $notification->read = true;
        $notification->save();
    }
    
    $manageadmins = Engine_Api::_()->getDbtable('manageadmins', 'sitegroup')->getManageAdmin($sitegroup->group_id);
    foreach($manageadmins as $manageadmin) {
      $user_subject = Engine_Api::_()->user()->getUser($manageadmin['user_id']);
      Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user_subject, $viewer, $sitegroup, 'sitegroup_join');
    }
     
    $message = Zend_Registry::get('Zend_Translate')->_('You have accepted the invite to the group %s');
    $message = sprintf($message, $sitegroup->__toString());
    $this->view->message = $message;

    if( null === $this->_helper->contextSwitch->getCurrentContext() ) {
      return $this->_forwardCustom('success', 'utility', 'core', array(
        'messages' => array($message),
        'layout' => 'default-simple',
        'parentRefresh' => true,
      ));
    }
  }

  //ACTION FOR REJECT GROUP MEMBER REQUEST.
  public function rejectAction() {

    //CHECK AUTH
    if( !$this->_helper->requireUser()->isValid() ) return;

    //PROCESS
    $viewer = Engine_Api::_()->user()->getViewer();
    
    //GET THE SITEGROUP ID FROM THE URL
    $group_id = $this->_getParam('group_id');
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);

    //MAKE FORM
    $this->view->form = $form = new Sitegroupmember_Form_Member();
    $form->setTitle('Reject Group Invitation');
    $form->setDescription('Would you like to reject the invitation for this group?');
    $form->submit->setLabel('Reject Group Invitation');
    
    //PROCESS FORM
    if( !$this->getRequest()->isPost() ) {
      $this->view->status = false;
      $this->view->error = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Invalid Method');
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      $this->view->status = false;
      $this->view->error = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Invalid Data');
      return;
    }

		if (!empty($group_id)) {
		
			//DELETE THE RESULT FORM THE TABLE.
			Engine_Api::_()->getDbtable('membership', 'sitegroup')->delete(array('resource_id =?' => $group_id, 'user_id =?' => $viewer->getIdentity()));
		}
		
    $this->view->status = true;
    $this->view->error = false;
    $message = Zend_Registry::get('Zend_Translate')->_('You have ignored the invite to the group %s');
    $message = sprintf($message, $sitegroup->__toString());
    $this->view->message = $message;

    // Set the request as handled
    $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
            $viewer, $sitegroup, 'sitegroupmember_approve');
    if ($notification) {
        $notification->mitigated = true;
        $notification->read = true;
        $notification->save();
    }   

    // Set the request as handled
    $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
            $viewer, $sitegroup, 'sitegroupmember_invite');
    if ($notification) {
        $notification->mitigated = true;
        $notification->read = true;
        $notification->save();
    }   
    
    if( null === $this->_helper->contextSwitch->getCurrentContext() ) {
      return $this->_forwardCustom('success', 'utility', 'core', array(
        'messages' => array($message),
        'layout' => 'default-simple',
        
        'parentRefresh' => true,
      ));
    }
  }
  
  //ACTION FOR THE INVITE MEMBER.
  public function inviteMembersAction() {

    if( !$this->_helper->requireUser()->isValid() ) return;

    //GET GROUP ID.
    $this->view->group_id = $group_id = $this->_getParam('group_id');
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
    
    $viewer = Engine_Api::_()->user()->getViewer();
    
		$isGroupAdmin = Engine_Api::_()->getDbtable('manageadmins', 'sitegroup')->isGroupAdmins($viewer->getIdentity(), $sitegroup->getIdentity());
		
    $automaticallyJoin = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'groupmember.automatically.addmember', 1);

    //PREPARE FORM
    $this->view->form = $form = new Sitegroupmember_Form_InviteMembers();
    
    //IF THE MODE IS APP MODE THEN
    if (Engine_Api::_()->seaocore()->isSitemobileApp()) {
      $memberSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'groupmember.automatically.addmember' , 1);
      if (!empty($memberSettings)) {
        $text = "Add People";
      }else{
        $text = "Invite People";
      }
      $this->view->sitemapPageHeaderTitle = $text;
      $form->setTitle('');
    }
    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }
    
    $values = $form->getValues();
    
    if(empty($values['user_ids']) && empty($values['toValues'])) {
        $form->addError('Please complete this field - It is requried.');
        return;
    }
    
    if(empty($values['toValues'])) {
        $form->addError('This is an invalid user name. Please select a valid user name from the autosuggest.');
        return;
    }

    $members_ids = explode(",", $values['toValues']);

    $membersTable = Engine_Api::_()->getDbtable('membership', 'sitegroup');
    
		if (!empty($members_ids)) {
		
			foreach($members_ids as $members_id) {

				$row = $membersTable->createRow();
				$row->resource_id = $group_id;
				$row->group_id = $group_id;
				$row->user_id = $members_id;
				$row->resource_approved = 1;
				
				if (!empty($automaticallyJoin) && !empty($sitegroup->member_approval) && empty($isGroupAdmin)) {
					$row->active = 1;
					$row->user_approved = 1;
                                        $row->save();
								
					//MEMBER COUNT INCREASE WHEN MEMBER JOIN THE GROUP.
          Engine_Api::_()->sitegroup()->updateMemberCount($sitegroup);
					$sitegroup->save();
				} elseif (!empty($automaticallyJoin) && !empty($isGroupAdmin)) {
					$row->active = 1;
					$row->user_approved = 1;
					$row->save();			
					//MEMBER COUNT INCREASE WHEN MEMBER JOIN THE GROUP.
          Engine_Api::_()->sitegroup()->updateMemberCount($sitegroup);
					$sitegroup->save();
				}
				else {
					$row->active = 0;
					//$row->resource_approved = 0;
					$row->user_approved = 0;
				}
				
				$row->save();
				
				if (empty($automaticallyJoin)) {
					$user_subject = Engine_Api::_()->user()->getUser($members_id);
					Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user_subject, $viewer, $sitegroup, 'sitegroupmember_invite');
				} 
				else {
				  $user_subject = Engine_Api::_()->user()->getUser($members_id);
					//SET THE REQUEST AS HANDLED FOR NOTIFACTION.
					Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user_subject, $viewer, $sitegroup, 'sitegroup_addmember');

					//ADD ACTIVITY
					$action=Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user_subject, $sitegroup, 'sitegroup_join');
					if ( $action ) {
						Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity( $action , $sitegroup ) ;
					}
	        Engine_Api::_()->getApi('subCore', 'sitegroup')->deleteFeedStream($action,true);
				}
			}
		}
		
    return $this->_forwardCustom('success', 'utility', 'core', array(
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('The selected members have been successfully added to this group.')),
      'layout' => 'default-simple',
      
      'parentRefresh' => true,
    ));
  }
  
  //ACTION FOR THE INVITE MEMBER.
  public function inviteAction() {

    if( !$this->_helper->requireUser()->isValid() ) return;

    //GET GROUP ID.
    $group_id = $this->_getParam('group_id');
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
    
    $automaticallyJoin = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'groupmember.automatically.addmember', 1);
    
    //PREPARE DATA
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->friends = $friends = $viewer->membership()->getMembers();

    $hasMembers_viewer = Engine_Api::_()->getDbTable('membership', 'sitegroup')->hasMembers($viewer->getIdentity(), $sitegroup->getIdentity());

    //PREPARE FORM
    $this->view->form = $form = new Sitegroupmember_Form_Invite();

    $count = 0;
    foreach( $friends as $friend ) {
    
			$friend_id = $friend->getIdentity();

			$hasMembers = Engine_Api::_()->getDbTable('membership', 'sitegroup')->hasMembers($friend_id, $group_id);

			if(!empty($hasMembers)) {
				continue;
		  }
      //if( $sitegroup->membership()->isMember($friend, null) ) continue;
      $multiOptions[$friend->getIdentity()] = $friend->getTitle();
      $count++;
    }
    sort($multiOptions);
    $form->users->addMultiOptions($multiOptions);
    $this->view->count = $count;

    // throw notice if count = 0
    if( $count == 0 ) {
      return $this->_forwardCustom('success', 'utility', 'core', array(
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('You have currently no friends to invite.')),
      'layout' => 'default-simple',
      'parentRefresh' => true,
      ));
    }

    //NOT POSTING
    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    $usersIds = $form->getValue('users');
		foreach( $friends as $friend ) {
		
			if( !in_array($friend->getIdentity(), $usersIds) ) {
				continue;
			}

			//GET VALUE FROM THE FORM.
			$values = $this->getRequest()->getPost();
			$membersTable = Engine_Api::_()->getDbtable('membership', 'sitegroup');
			$row = $membersTable->createRow();
			$row->resource_id = $group_id;
			$row->group_id = $group_id;
			$row->user_id = $friend->getIdentity();
			$row->resource_approved = 1;
			
			if (!empty($automaticallyJoin) && !empty($sitegroup->member_approval)) {
				$row->active = 1;
				$row->user_approved = 1;

				//MEMBER COUNT INCREASE WHEN MEMBER JOIN THE GROUP.
        Engine_Api::_()->sitegroup()->updateMemberCount($sitegroup);
				$sitegroup->save();
			} else {
				$row->active = 0;
				if(!empty($automaticallyJoin) && empty($sitegroup->member_approval)) {
					$row->resource_approved = 0;
				}
				$row->user_approved = 0;
			}
			
			$row->save();
			
      if (empty($automaticallyJoin)) {
				Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($friend, $viewer, $sitegroup, 'sitegroupmember_invite');
			} elseif (!empty($automaticallyJoin) && empty($sitegroup->member_approval)) {
				Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($friend, $viewer, $sitegroup, 'sitegroupmember_invite');
			}
		}
    if (!empty($automaticallyJoin) && !empty($sitegroup->member_approval)) {
			$messages = Zend_Registry::get('Zend_Translate')->_('Members have been successfully added.');
    } else {
			$messages = Zend_Registry::get('Zend_Translate')->_('Members have been successfully invited.');
    }
    return $this->_forwardCustom('success', 'utility', 'core', array(
      'messages' => array(Zend_Registry::get('Zend_Translate')->_($messages)),
      'layout' => 'default-simple',
      'parentRefresh' => true,
    ));
  }
  
  //ACTION FOR JOINED MORE GROUPS BY MEMBERS.
  public function getMoreJoinedGroupsAction() {
  
		$group_title = $this->_getParam('text', null); 
		$user_id = $this->_getParam('user_id', null);
    $data = array();

    $joinGroup = Engine_Api::_()->getDbtable('membership', 'sitegroup')->getJoinGroups($user_id, 'memberOfDay');

    $groupIds = array();

    foreach( $joinGroup as $joinGroups ) {
      $groupIds[] = $joinGroups['group_id'];
    }

    $moregroups = Engine_Api::_()->sitegroupmember()->getMoreGroup($groupIds, $group_title);

    foreach ($moregroups as $moregroup) {
			$group_photo = $this->view->itemPhoto($moregroup, 'thumb.icon');
      $data[] = array(
				'id' => $moregroup->group_id,
				'label' => $moregroup->title,
				'photo' => $group_photo
      );
    }

    return $this->_helper->json($data);
  }
  
  //ACTION FOR JOINED MERE GROUPS.
  public function joinedMoreGroupsAction() {
  
    $this->view->user_id = $user_id = $this->_getParam('user_id');
    
    //SET LAYOUT
    $this->_helper->layout->setLayout('default-simple');

    //FORM GENERATION
    $form = $this->view->form = new Sitegroupmember_Form_JoinedMoreGroups();

    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));
    //$form->getElement('title')->setLabel('Enter the name of the group which you want to join.');

    $viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();

    //PROCESS FORM
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )	{

			//GET VALUE FROM THE FORM.
			$values = $this->getRequest()->getPost();
			$group_id = $values['group_id'];
			$sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
			$owner = $sitegroup->getOwner();

			//SET THE REQUEST AS HANDLED FOR NOTIFACTION.
			Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($owner, $viewer, $sitegroup, 'sitegroup_join');
			
			//ADD ACTIVITY
			$action=Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $sitegroup, 'sitegroup_join');
		  if ( $action ) {
				Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity( $action , $sitegroup ) ;
			}
	    Engine_Api::_()->getApi('subCore', 'sitegroup')->deleteFeedStream($action,true);

			$membersTable = Engine_Api::_()->getDbtable('membership', 'sitegroup');
			$row = $membersTable->createRow();
			$row->resource_id = $group_id;
			$row->group_id = $group_id;
			$row->user_id = $viewer_id;
			
			//IF MEMBER IS ALREADY FEATURED THEN AUTOMATICALLY FEATURED WHEN MEMBER JOIN THE ANY GROUP.
			$sitegroupmember = Engine_Api::_()->getDbTable('membership', 'sitegroup')->hasMembers($viewer_id);
			if(!empty($sitegroupmember->featured) && $sitegroupmember->featured == 1) {
				$row->featured = 1;
			}

			$row->save();

      //MEMBER COUNT INCREASE WHEN MEMBER JOIN THE GROUP.
      Engine_Api::_()->sitegroup()->updateMemberCount($sitegroup);
			$sitegroup->save();

		  //AUTOMATICALLY LIKE THE GROUP WHEN MEMBER JOIN THE GROUP.
		  $autoLike = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'groupmember.automatically.like' , 0);
		  if(!empty($autoLike)) {
				Engine_Api::_()->sitegroup()->autoLike($group_id, 'sitegroup_group');
      }

      return $this->_forwardCustom('success', 'utility', 'core', array(
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('You are now a member of this group.')),
        'layout' => 'default-simple',
        'parentRefresh' => true,
      ));
    }
  }

  public function requestMemberAction() {
    $this->view->notification = $notification = $this->_getParam('notification');
  }
}