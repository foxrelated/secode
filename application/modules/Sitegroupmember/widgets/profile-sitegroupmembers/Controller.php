<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupmember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroupmember_Widget_ProfileSitegroupmembersController extends Engine_Content_Widget_Abstract {

  protected $_childCount;

  public function indexAction() {

    //DONT RENDER THIS IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('sitegroup_group')) {
      return $this->setNoRender();
    }
    $this->view->params = $params = $this->_getAllParams();
    //GET SITEGROUP SUBJECT
    $this->view->sitegroup = $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');

    if (!Engine_Api::_()->sitegroup()->allowInThisGroup($sitegroup, "sitegroupmember", 'smecreate')) {
      return $this->setNoRender();
    }
    
//    //START MANAGE-ADMIN CHECK
//    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'view');
//    if (empty($isManageAdmin)) {
//      return $this->setNoRender();
//    }

    $rolesTable = Engine_Api::_()->getDbtable('roles', 'sitegroupmember');

    $this->view->show_option = $show_option = $this->_getParam('show_option', 1);
    $this->view->roles_id = $roles_id = $this->_getParam('roles_id', null);
//    $isajax = $this->_getParam('isajax', null);

    $rolesParams = array();
    if ($show_option == 0) {
      $othersRoles = in_array('0', $roles_id);
      $groupAdminRoles = in_array('groupadminRole', $roles_id);

//      if ($othersRoles) {
//				if (!empty($role_id)) {
//					unset($role_id[array_search('0', $role_id)]);
//				}
//      }

      if (!empty($roles_id))
        $rolesParams = $rolesTable->getSiteAdminRoles(array("group_category_id" => $sitegroup->category_id, 'role_ids' => $roles_id));

      if (!empty($groupAdminRoles)) {
        $groupAdminRole = $rolesTable->getGroupadminsRoles($sitegroup->group_id);
        $rolesParams = array_merge($rolesParams, $groupAdminRole);
      }
      if ($othersRoles) {
        $rolesParams[] = '0';
      }
      if (count($rolesParams) < 1)
        return $this->setNoRender();
    }
    //}
    
    $sitegroupmemberProfile = Zend_Registry::isRegistered('sitegroupmemberProfile') ? Zend_Registry::get('sitegroupmemberProfile') : null;
    if (empty($sitegroupmemberProfile)) {
      return $this->setNoRender();
    }

    //GET VIEWER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

//     $groupMemberPhraseNum = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroupmember.phrase.num', null);
// 
//     $memberJoinType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroupmember.join.type', null);
//     if ($memberJoinType != $groupMemberPhraseNum) {
//       return $this->setNoRender();
//     }   

    //TOTAL members
    $memberCount = Engine_Api::_()->sitegroup()->getTotalCount($sitegroup->group_id, 'sitegroup', 'membership');

    $this->view->can_edit = $can_edit = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');

    if (empty($memberCount) && empty($can_edit)) {
      return $this->setNoRender();
    }

    $membershipTable = Engine_Api::_()->getDbtable('membership', 'sitegroup');    
    
    $values = array();
    //END MANAGE-ADMIN CHECK
    if (!empty($rolesParams)) {
      $values['roles_id'] = $rolesParams;
    }
    $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.layoutcreate', 0);
    $this->view->widgets = $widgets = Engine_Api::_()->sitegroup()->getwidget($layout, $sitegroup->group_id);

    $this->view->module_tabid = $currenttabid = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);
    $isajax = $this->_getParam('is_ajax_load', null);
    $this->view->isajax = $isajax;
    $this->view->showtoptitle = $showtoptitle = Engine_Api::_()->sitegroup()->showtoptitle($layout, $sitegroup->group_id);

    if (!empty($isajax) || ($currenttabid == $this->view->identity) || ($widgets == 0)) {
      $this->view->identity_temp = Zend_Controller_Front::getInstance()->getRequest()->getParam('identity_temp', $currenttabid);
      $this->view->show_content = true;

      //GET SEARCHING PARAMETERS
      $this->view->group = $group = $this->_getParam('group', 1);
      $this->view->search = $search = $this->_getParam('search');
      $this->view->search_text = $search = $this->_getParam('search_text');
      $this->view->role_id = $role_id = $this->_getParam('role_id', 0);
      $this->view->visibility = $selectbox = $this->_getParam('visibility');
      $this->view->member_encoded = $member_encoded = (int) $this->_getParam('member_encoded', 1);

      if (!empty($search)) {
        $values['search'] = $search;
      }

      if (!empty($selectbox)) {
        $values['orderby'] = $selectbox;
      }

      $values['group_id'] = $sitegroup->group_id;

      if (!empty($role_id)) {
        $values['roles_id'] = $role_id > 0 ? array($role_id) : array(0);
      }

      $this->view->request_count = $membershipTable->getSitegroupmembersPaginator($values, 'request');

      //MAKE PAGINATOR
      $currentGroupNumber = $this->_getParam('group', 1);

      $this->view->paginator = $paginator = $membershipTable->getSitegroupmembersPaginator($values);
      //if ($paginator->getTotalItemCount() == 0) return $this->setNoRender();
      $paginator->setItemCountPerPage(24)->setCurrentPageNumber($currentGroupNumber);

      //ADD NUMBER OF POLLS IN TAB
      if ($this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0) {
        $this->_childCount = $paginator->getTotalItemCount();
      } else {
        if (empty($this->view->search) && empty($can_edit)) {
          return $this->setNoRender();
        }
      }
      
        //CHECK IF USER IS JOIN THE GROUP OR NOT.
        //$friendId = $viewer->membership()->getMembershipsOfIds();
        $select = $membershipTable->hasMembers($viewer_id, $sitegroup->group_id);
        if (!empty($select)) {
          $this->view->hasMember = 1;
        }      
        
        $this->view->roleParamsArray = $rolesTable->rolesParams(array($sitegroup->category_id), 0, $rolesParams, 1,  $sitegroup->group_id);         
      
    } else {
      $this->view->show_content = false;
      $this->view->identity_temp = $this->view->identity;
      $values['group_id'] = $sitegroup->group_id;
      $values['show_count'] = 1;

      if ($can_edit) {
        $this->view->request_count = $membershipTable->getSitegroupmembersPaginator($values, 'request');

        $this->view->paginator = $paginator = $membershipTable->getSitegroupmembersPaginator($values);

        if ($paginator->getTotalItemCount() > 0) {
          $this->_childCount = $paginator->getTotalItemCount();
        }
      } else {
        $this->view->request_count = $membershipTable->getSitegroupmembersPaginator($values, 'request');
        $this->view->paginator = $paginator = $membershipTable->getSitegroupmembersPaginator($values);

        if ($paginator->getTotalItemCount() == 0)
          return $this->setNoRender();

        if ($paginator->getTotalItemCount() > 0) {
          $this->_childCount = $paginator->getTotalItemCount();
        }
      }
    }
    $this->view->user_layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.layoutcreate', 0);

		if($sitegroup->member_title && $sitegroup->member_count > 1 && Engine_Api::_()->getApi('settings', 'core')->getSetting( 'groupmember.member.title' , 1) && $show_option){
				$this->getElement()->setTitle($sitegroup->member_title);
		}
  }

  public function getChildCount() {
    return $this->_childCount;
  }

}
